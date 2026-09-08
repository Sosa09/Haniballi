<?php

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Clinical Telehealth Consultation - Dr. Mehdi Haniballi')] class extends Component
{
    public ?Appointment $appointment = null;
    public ?User $currentUser = null;
    public string $userRole = 'patient';
    public bool $isDoctor = false;
    public int $appointmentId = 0;
    public string $peerName = '';

    public function mount(int $appointmentId): void
    {
        $this->appointmentId = $appointmentId;
        $this->currentUser = Auth::user();
        $this->userRole = $this->currentUser?->role ?? 'patient';
        $this->isDoctor = $this->userRole === 'doctor';

        $this->appointment = Appointment::with(['patient.user', 'user', 'videoSession'])
            ->findOrFail($appointmentId);

        if ($this->isDoctor) {
            $this->peerName = $this->appointment->patient?->full_name ?? 'Patient';
        } else {
            $this->peerName = 'Dr. Mehdi Haniballi';
        }
    }
};
?>

<div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('telehealthStudio', (config) => ({
                appointmentId: config.appointmentId,
                userRole: config.userRole,
                userName: config.userName,
                peerName: config.peerName,
                isDoctor: config.isDoctor,
                clientId: 'cli_' + Math.random().toString(36).substring(2, 10),

                // Call state
                inCall: false,
                connectionStatus: 'idle', // 'idle' | 'initializing' | 'connecting' | 'connected' | 'reconnecting' | 'ended'
                connectionQuality: 'optimal',
                hasRemoteStream: false,
                usingSyntheticVideo: false,
                isMutedByPeer: false,
                peerOnline: false,
                isInsecureMobile: false,

                // Offer / Answer handshake locks
                isMakingOffer: false,
                hasInitiatedOffer: false,

                // Hardware Controls
                micOn: true,
                camOn: true,
                screenSharing: false,
                audioLevel: 10,

                // Workspace & Session
                activeTab: 'notes',
                callDurationSeconds: 0,
                timerInterval: null,
                pollingInterval: null,
                audioAnalyserInterval: null,
                lastSignalId: 0,

                // Clinical Notes
                clinicalNotes: config.initialNotes || '',
                notesSaving: false,
                notesSaved: false,

                // Chat
                chatMessages: [
                    { sender: 'System', text: 'Encrypted peer-to-peer session initialized.', time: 'Now', isSystem: true }
                ],
                newMessage: '',

                // WebRTC Core
                localStream: null,
                remoteStream: null,
                peerConnection: null,
                screenStream: null,
                audioContext: null,
                analyser: null,
                queuedCandidates: [],

                rtcConfig: {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        { urls: 'stun:stun2.l.google.com:19302' },
                        { urls: 'stun:stun3.l.google.com:19302' },
                        { urls: 'stun:stun4.l.google.com:19302' }
                    ]
                },

                get formattedDuration() {
                    const hrs = Math.floor(this.callDurationSeconds / 3600);
                    const mins = Math.floor((this.callDurationSeconds % 3600) / 60);
                    const secs = this.callDurationSeconds % 60;
                    if (hrs > 0) {
                        return String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                    }
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                },

                async initSession() {
                    this.inCall = true;
                    this.connectionStatus = 'initializing';
                    this.callDurationSeconds = 0;
                    this.hasInitiatedOffer = false;

                    const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
                    const isSecure = window.isSecureContext || isLocal;
                    this.isInsecureMobile = !isSecure;

                    try {
                        if (isSecure && navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
                            this.localStream = await navigator.mediaDevices.getUserMedia({
                                video: { width: { ideal: 1280 }, height: { ideal: 720 } },
                                audio: true
                            });
                            this.usingSyntheticVideo = false;
                        } else {
                            console.warn('[WebRTC] Insecure context detected; activating resilient mobile stream fallback.');
                            this.localStream = this.createSyntheticStream(this.userName);
                            this.usingSyntheticVideo = true;
                        }
                    } catch (err) {
                        console.warn('[WebRTC] Camera access denied or hardware unavailable, fallback to synthetic stream:', err);
                        this.localStream = this.createSyntheticStream(this.userName);
                        this.usingSyntheticVideo = true;
                    }

                    this.$nextTick(() => {
                        const localVid = document.getElementById('localVideo');
                        if (localVid && this.localStream) {
                            localVid.srcObject = this.localStream;
                            localVid.play().catch(e => console.log('Local video play notice:', e));
                        }
                    });

                    this.initAudioAnalyser(this.localStream);
                    this.createPeerConnection();

                    this.timerInterval = setInterval(() => {
                        this.callDurationSeconds++;
                    }, 1000);

                    this.startSignalingPolling();
                    await this.sendSignal('status', { status: 'joined', role: this.userRole, name: this.userName });

                    setTimeout(async () => {
                        if (this.inCall && !this.hasInitiatedOffer && this.peerConnection && this.peerConnection.signalingState === 'stable') {
                            console.log('[WebRTC] Auto-initiating offer after room startup...');
                            await this.initiateOffer();
                        }
                    }, 750);
                },

                createPeerConnection() {
                    try {
                        console.log('[WebRTC] Creating RTCPeerConnection...');
                        this.peerConnection = new RTCPeerConnection(this.rtcConfig);

                        const localTracks = this.localStream ? this.localStream.getTracks() : [];
                        if (localTracks.length > 0) {
                            localTracks.forEach(track => {
                                this.peerConnection.addTrack(track, this.localStream);
                                console.log('[WebRTC] Added local track:', track.kind);
                            });
                        } else {
                            console.log('[WebRTC] No local tracks, attaching receive-only transceivers');
                            this.peerConnection.addTransceiver('audio', { direction: 'recvonly' });
                            this.peerConnection.addTransceiver('video', { direction: 'recvonly' });
                        }

                        this.peerConnection.ontrack = (event) => {
                            console.log('[WebRTC] Received remote track:', event.track.kind);
                            this.remoteStream = event.streams[0] || new MediaStream([event.track]);
                            this.hasRemoteStream = true;
                            this.connectionStatus = 'connected';
                            this.$nextTick(() => {
                                const remoteVid = document.getElementById('remoteVideo');
                                if (remoteVid) {
                                    remoteVid.srcObject = this.remoteStream;
                                    remoteVid.play().catch(e => console.log('Remote play notice:', e));
                                }
                            });
                        };

                        this.peerConnection.onicecandidate = (event) => {
                            if (event.candidate) {
                                this.sendSignal('candidate', event.candidate.toJSON());
                            }
                        };

                        this.peerConnection.oniceconnectionstatechange = () => {
                            const ice = this.peerConnection.iceConnectionState;
                            console.log('[WebRTC] ICE state changed to:', ice);
                            if (ice === 'connected' || ice === 'completed') {
                                this.connectionStatus = 'connected';
                            } else if (ice === 'disconnected' || ice === 'failed') {
                                this.connectionStatus = 'reconnecting';
                            }
                        };

                        this.peerConnection.onconnectionstatechange = () => {
                            const state = this.peerConnection.connectionState;
                            console.log('[WebRTC] PeerConnection state changed to:', state);
                            if (state === 'connected') {
                                this.connectionStatus = 'connected';
                            } else if (state === 'connecting') {
                                if (this.connectionStatus !== 'connected') {
                                    this.connectionStatus = 'connecting';
                                }
                            } else if (state === 'disconnected' || state === 'failed') {
                                this.connectionStatus = 'reconnecting';
                            } else if (state === 'closed') {
                                this.connectionStatus = 'ended';
                            }
                        };
                    } catch (e) {
                        console.error('[WebRTC] RTCPeerConnection setup failed:', e);
                    }
                },

                async initiateOffer(force = false) {
                    if (!this.peerConnection) return;
                    if (this.isMakingOffer) return;
                    if (this.hasInitiatedOffer && !force) return;
                    if (!force && this.peerConnection.signalingState !== 'stable') {
                        console.warn('[WebRTC] Cannot initiate offer when signalingState is', this.peerConnection.signalingState);
                        return;
                    }

                    this.isMakingOffer = true;
                    try {
                        console.log('[WebRTC] Initiating SDP offer...');
                        const offer = await this.peerConnection.createOffer({
                            offerToReceiveAudio: true,
                            offerToReceiveVideo: true
                        });
                        await this.peerConnection.setLocalDescription(offer);
                        this.hasInitiatedOffer = true;
                        await this.sendSignal('offer', offer);
                        console.log('[WebRTC] Offer dispatched successfully.');
                    } catch (err) {
                        console.error('[WebRTC] Failed to create offer:', err);
                    } finally {
                        this.isMakingOffer = false;
                    }
                },

                async handleIncomingSignal(sig) {
                    if (!this.peerConnection) return;

                    try {
                        if (sig.type === 'offer') {
                            console.log('[WebRTC] Remote offer received. Current state:', this.peerConnection.signalingState);
                            
                            const isPolite = this.clientId > (sig.client_id || '');
                            if (this.peerConnection.signalingState !== 'stable') {
                                if (!isPolite) {
                                    console.warn('[WebRTC] State is not stable. We are impolite, ignoring remote offer.');
                                    return;
                                }
                                console.warn('[WebRTC] State is not stable, but we are polite. Rolling back local offer.');
                                await this.peerConnection.setLocalDescription({ type: 'rollback' });
                            }

                            // Laravel TrimStrings middleware strips the trailing \r\n from the SDP, causing Invalid SDP errors!
                            if (sig.payload && typeof sig.payload.sdp === 'string') {
                                if (!sig.payload.sdp.endsWith('\r\n')) {
                                    sig.payload.sdp += '\r\n';
                                }
                            }

                            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(sig.payload));
                            console.log('[WebRTC] Remote description (offer) set');

                            while (this.queuedCandidates.length > 0) {
                                const cand = this.queuedCandidates.shift();
                                try {
                                    await this.peerConnection.addIceCandidate(new RTCIceCandidate(cand));
                                } catch (e) {
                                    console.warn('[WebRTC] Candidate error:', e);
                                }
                            }

                            const answer = await this.peerConnection.createAnswer();
                            await this.peerConnection.setLocalDescription(answer);
                            await this.sendSignal('answer', answer);
                            console.log('[WebRTC] Answer generated and dispatched');

                        } else if (sig.type === 'answer') {
                            console.log('[WebRTC] Remote answer received. Current state:', this.peerConnection.signalingState);
                            if (this.peerConnection.signalingState === 'have-local-offer') {
                                // Laravel TrimStrings middleware strips the trailing \r\n from the SDP, causing Invalid SDP errors!
                                if (sig.payload && typeof sig.payload.sdp === 'string') {
                                    if (!sig.payload.sdp.endsWith('\r\n')) {
                                        sig.payload.sdp += '\r\n';
                                    }
                                }

                                await this.peerConnection.setRemoteDescription(new RTCSessionDescription(sig.payload));
                                console.log('[WebRTC] Remote description (answer) set successfully');

                                while (this.queuedCandidates.length > 0) {
                                    const cand = this.queuedCandidates.shift();
                                    try {
                                        await this.peerConnection.addIceCandidate(new RTCIceCandidate(cand));
                                    } catch (e) {
                                        console.warn('[WebRTC] Candidate error:', e);
                                    }
                                }
                            } else {
                                console.warn('[WebRTC] Ignored answer because signalingState is not have-local-offer');
                            }

                        } else if (sig.type === 'candidate') {
                            if (this.peerConnection.remoteDescription && this.peerConnection.remoteDescription.type) {
                                try {
                                    await this.peerConnection.addIceCandidate(new RTCIceCandidate(sig.payload));
                                } catch (e) {
                                    console.warn('[WebRTC] Ice candidate add warning:', e);
                                }
                            } else {
                                this.queuedCandidates.push(sig.payload);
                            }

                        } else if (sig.type === 'chat') {
                            this.chatMessages.push({
                                sender: sig.payload.sender || this.peerName,
                                text: sig.payload.text,
                                time: sig.payload.time || 'Now',
                                isMe: false
                            });

                        } else if (sig.type === 'status') {
                            if (sig.payload.status === 'joined') {
                                this.peerOnline = true;
                                if (!this.hasInitiatedOffer && this.peerConnection && this.peerConnection.signalingState === 'stable') {
                                    console.log('[WebRTC] Peer joined announcement received, generating offer...');
                                    await this.initiateOffer();
                                }
                            }

                        } else if (sig.type === 'leave') {
                            this.hasRemoteStream = false;
                            this.peerOnline = false;
                            this.hasInitiatedOffer = false;
                            this.chatMessages.push({
                                sender: 'System',
                                text: this.peerName + ' has exited the consultation.',
                                time: 'Now',
                                isSystem: true
                            });
                        }
                    } catch (err) {
                        console.error('[WebRTC] Signal handling error for type ' + sig.type + ':', err);
                    }
                },

                startSignalingPolling() {
                    this.pollSignals();
                    this.pollingInterval = setInterval(() => {
                        this.pollSignals();
                    }, 1200);
                },

                async pollSignals() {
                    if (!this.inCall) return;

                    try {
                        const res = await fetch(`{{ url('/video') }}/${this.appointmentId}/signals?last_id=${this.lastSignalId}&client_id=${encodeURIComponent(this.clientId)}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (res.ok) {
                            const data = await res.json();
                            this.lastSignalId = data.last_id;
                            const wasPeerOnline = this.peerOnline;
                            this.peerOnline = data.peer_online;

                            if (this.peerOnline && !wasPeerOnline && !this.hasInitiatedOffer && this.peerConnection && this.peerConnection.signalingState === 'stable') {
                                console.log('[WebRTC] Peer came online! Auto-initiating offer...');
                                await this.initiateOffer();
                            }

                            if (data.signals && data.signals.length > 0) {
                                for (const sig of data.signals) {
                                    await this.handleIncomingSignal(sig);
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('[WebRTC] Signal polling network lag:', e);
                    }
                },

                async sendSignal(type, payload) {
                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';

                        await fetch(`{{ url('/video') }}/${this.appointmentId}/signal`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ type, payload, client_id: this.clientId })
                        });
                    } catch (err) {
                        console.error('[WebRTC] Signal dispatch failure:', err);
                    }
                },

                toggleMic() {
                    if (!this.localStream) return;
                    this.micOn = !this.micOn;
                    this.localStream.getAudioTracks().forEach(track => {
                        track.enabled = this.micOn;
                    });
                },

                toggleCam() {
                    if (!this.localStream) return;
                    this.camOn = !this.camOn;
                    this.localStream.getVideoTracks().forEach(track => {
                        track.enabled = this.camOn;
                    });
                },

                async toggleScreenShare() {
                    if (!this.screenSharing) {
                        try {
                            this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                            const screenTrack = this.screenStream.getVideoTracks()[0];

                            if (this.peerConnection) {
                                const sender = this.peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
                                if (sender) {
                                    sender.replaceTrack(screenTrack);
                                }
                            }

                            const localVid = document.getElementById('localVideo');
                            if (localVid) {
                                localVid.srcObject = this.screenStream;
                            }

                            screenTrack.onended = () => {
                                this.stopScreenShare();
                            };

                            this.screenSharing = true;
                        } catch (err) {
                            console.warn('Screen share cancelled or rejected:', err);
                        }
                    } else {
                        this.stopScreenShare();
                    }
                },

                stopScreenShare() {
                    if (this.screenStream) {
                        this.screenStream.getTracks().forEach(t => t.stop());
                        this.screenStream = null;
                    }

                    if (this.peerConnection && this.localStream) {
                        const camTrack = this.localStream.getVideoTracks()[0];
                        const sender = this.peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
                        if (sender && camTrack) {
                            sender.replaceTrack(camTrack);
                        }
                    }

                    const localVid = document.getElementById('localVideo');
                    if (localVid && this.localStream) {
                        localVid.srcObject = this.localStream;
                    }

                    this.screenSharing = false;
                },

                async sendChatMessage() {
                    if (!this.newMessage.trim()) return;
                    const msgText = this.newMessage.trim();
                    const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    this.chatMessages.push({
                        sender: 'You',
                        text: msgText,
                        time: nowTime,
                        isMe: true
                    });

                    this.newMessage = '';

                    await this.sendSignal('chat', {
                        sender: this.userName,
                        text: msgText,
                        time: nowTime
                    });
                },

                async saveClinicalNotes() {
                    this.notesSaving = true;
                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';

                        const res = await fetch(`{{ url('/video') }}/${this.appointmentId}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ notes: this.clinicalNotes })
                        });

                        if (res.ok) {
                            this.notesSaved = true;
                            setTimeout(() => { this.notesSaved = false; }, 3000);
                        }
                    } catch (e) {
                        console.error('Note save error:', e);
                    } finally {
                        this.notesSaving = false;
                    }
                },

                async endCall() {
                    if (this.localStream) {
                        this.localStream.getTracks().forEach(t => t.stop());
                    }
                    if (this.screenStream) {
                        this.screenStream.getTracks().forEach(t => t.stop());
                    }

                    if (this.peerConnection) {
                        this.peerConnection.close();
                        this.peerConnection = null;
                    }

                    clearInterval(this.timerInterval);
                    clearInterval(this.pollingInterval);
                    clearInterval(this.audioAnalyserInterval);

                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';
                        await fetch(`{{ url('/video') }}/${this.appointmentId}/leave`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ client_id: this.clientId })
                        });
                    } catch (e) {}

                    this.inCall = false;
                    this.connectionStatus = 'ended';
                },

                createSyntheticStream(label) {
                    try {
                        const canvas = document.createElement('canvas');
                        canvas.width = 640;
                        canvas.height = 360;
                        const ctx = canvas.getContext('2d');

                        let frame = 0;
                        const render = () => {
                            frame++;
                            const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
                            grad.addColorStop(0, '#0f172a');
                            grad.addColorStop(1, '#022c22');
                            ctx.fillStyle = grad;
                            ctx.fillRect(0, 0, canvas.width, canvas.height);

                            ctx.strokeStyle = '#065f46';
                            ctx.lineWidth = 1;
                            const cx = canvas.width / 2;
                            const cy = canvas.height / 2;
                            for (let r = 40; r <= 140; r += 35) {
                                ctx.beginPath();
                                ctx.arc(cx, cy, r + Math.sin(frame * 0.05) * 4, 0, Math.PI * 2);
                                ctx.stroke();
                            }

                            ctx.fillStyle = '#047857';
                            ctx.beginPath();
                            ctx.arc(cx, cy, 38, 0, Math.PI * 2);
                            ctx.fill();

                            ctx.fillStyle = '#ffffff';
                            ctx.font = 'bold 20px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            const initials = (label || 'CL').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                            ctx.fillText(initials, cx, cy);

                            ctx.fillStyle = '#94a3b8';
                            ctx.font = '12px sans-serif';
                            ctx.fillText((label || 'Consultation Feed') + ' (Mobile Spectator Feed)', cx, cy + 65);

                            ctx.fillStyle = '#10b981';
                            ctx.font = '10px monospace';
                            ctx.fillText('WebRTC Data Stream Active · 30 FPS', cx, cy + 85);

                            requestAnimationFrame(render);
                        };
                        requestAnimationFrame(render);

                        let videoStream = null;
                        if (typeof canvas.captureStream === 'function') {
                            videoStream = canvas.captureStream(30);
                        } else if (typeof canvas.mozCaptureStream === 'function') {
                            videoStream = canvas.mozCaptureStream(30);
                        }

                        let audioTrack = null;
                        try {
                            const AudioCtx = window.AudioContext || window.webkitAudioContext;
                            if (AudioCtx) {
                                const actx = new AudioCtx();
                                const osc = actx.createOscillator();
                                const dest = actx.createMediaStreamDestination();
                                const gain = actx.createGain();
                                gain.gain.value = 0.0;
                                osc.connect(gain);
                                gain.connect(dest);
                                osc.start();
                                audioTrack = dest.stream.getAudioTracks()[0];
                            }
                        } catch (e) {}

                        const tracks = [];
                        if (videoStream && videoStream.getVideoTracks().length > 0) {
                            tracks.push(...videoStream.getVideoTracks());
                        }
                        if (audioTrack) {
                            tracks.push(audioTrack);
                        }

                        return new MediaStream(tracks);
                    } catch (err) {
                        console.warn('[WebRTC] Fallback to empty MediaStream:', err);
                        return new MediaStream();
                    }
                },

                initAudioAnalyser(stream) {
                    if (!stream || stream.getAudioTracks().length === 0) {
                        this.audioLevel = 10;
                        return;
                    }
                    try {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtx) return;
                        const actx = new AudioCtx();
                        const source = actx.createMediaStreamSource(stream);
                        this.analyser = actx.createAnalyser();
                        this.analyser.fftSize = 64;
                        source.connect(this.analyser);

                        const dataArray = new Uint8Array(this.analyser.frequencyBinCount);

                        this.audioAnalyserInterval = setInterval(() => {
                            if (!this.micOn) {
                                this.audioLevel = 0;
                                return;
                            }
                            this.analyser.getByteFrequencyData(dataArray);
                            let sum = 0;
                            for (let i = 0; i < dataArray.length; i++) {
                                sum += dataArray[i];
                            }
                            const avg = sum / dataArray.length;
                            this.audioLevel = Math.min(100, Math.round(avg * 1.8));
                        }, 100);
                    } catch (e) {
                        this.audioLevel = 10;
                    }
                }
            }));
        });
    </script>

    <div class="max-w-7xl mx-auto space-y-6" x-data="telehealthStudio({
        appointmentId: {{ $appointmentId }},
        userRole: @js($userRole),
        userName: @js($currentUser?->name ?? 'User'),
        peerName: @js($peerName),
        isDoctor: {{ $isDoctor ? 'true' : 'false' }},
        initialNotes: @js($appointment->notes ?? '')
    })">

        @if ($appointment)
            {{-- Mobile Local Network Security Banner --}}
            <div x-show="isInsecureMobile" x-cloak class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-900 flex items-start gap-3 text-xs leading-relaxed shadow-sm">
                <div class="w-6 h-6 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0 text-amber-700 font-bold mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <strong class="font-bold text-amber-950">Mobile Browser Notice (HTTP Network):</strong>
                    Chrome and Safari on smartphones strictly require HTTPS for physical camera input.
                    Your phone is connected in <strong>Live Audio & Spectator Mode</strong> (you can see and hear the consultation room live).
                    To turn on your phone's physical camera, run <code class="bg-amber-100/80 px-1 py-0.5 rounded text-[11px] font-mono">herd share</code> for instant HTTPS, or toggle insecure origins in <code class="bg-amber-100/80 px-1 py-0.5 rounded text-[11px] font-mono">chrome://flags</code>.
                </div>
            </div>
            {{-- Session Top Bar --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 md:p-5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-emerald-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-base md:text-lg font-bold text-slate-900">
                                Telehealth Consultation Room #{{ $appointment->id }}
                            </h1>
                            <template x-if="inCall && connectionStatus === 'connected'">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                    ENCRYPTED P2P LIVE
                                </span>
                            </template>
                            <template x-if="inCall && connectionStatus !== 'connected'">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-ping"></span>
                                    <span x-text="connectionStatus === 'initializing' ? 'INITIALIZING...' : 'HANDSHAKING...'"></span>
                                </span>
                            </template>
                            <template x-if="!inCall">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    WAITING ROOM
                                </span>
                            </template>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $appointment->scheduled_at->format('l, d F Y · H:i') }} · Patient: <span class="font-semibold text-slate-700">{{ $appointment->patient?->full_name ?? 'Client' }}</span> · Specialist: <span class="font-semibold text-slate-700">Dr. Mehdi Haniballi</span>
                        </p>
                    </div>
                </div>

                {{-- Call Action Buttons & Clock --}}
                <div class="flex items-center gap-3">
                    <template x-if="inCall">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 text-white font-mono text-xs border border-slate-800 shadow-inner">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span x-text="formattedDuration">00:00</span>
                            </div>
                            <button
                                @click="endCall()"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
                                <span>End Session</span>
                            </button>
                        </div>
                    </template>

                    <template x-if="!inCall">
                        <button
                            @click="initSession()"
                            class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs md:text-sm font-bold transition shadow-sm hover:shadow flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Start Consultation</span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Main Telehealth Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Video Canvas Area (2 Columns) --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="relative bg-slate-950 rounded-2xl overflow-hidden shadow-xl border border-slate-800 aspect-video flex flex-col justify-between p-4 sm:p-5 select-none">

                        {{-- 1. In-Call Video Viewports --}}
                        <template x-if="inCall">
                            <div class="absolute inset-0 w-full h-full">

                                {{-- Remote Stream Video Element (Main Stage) --}}
                                <video
                                    id="remoteVideo"
                                    autoplay
                                    playsinline
                                    class="w-full h-full object-cover"
                                    :class="hasRemoteStream ? 'block' : 'hidden'">
                                </video>

                                {{-- Remote Placeholder when Peer hasn't joined or connected yet --}}
                                <div
                                    x-show="!hasRemoteStream"
                                    class="w-full h-full flex flex-col items-center justify-center text-center p-6 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950/40">
                                    <div class="relative mb-4">
                                        <div class="w-24 h-24 rounded-full bg-slate-800/80 border-2 border-emerald-500/40 flex items-center justify-center text-white shadow-2xl">
                                            <svg class="w-12 h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-slate-950" :class="peerOnline ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                                    </div>
                                    <h3 class="text-white text-base font-bold" x-text="peerName"></h3>
                                    <p class="text-slate-400 text-sm max-w-sm" x-show="peerOnline">
                                        Peer detected in waiting room. Establishing direct encrypted connection...
                                    </p>
                                    <button x-show="peerOnline && !hasRemoteStream" @click="initiateOffer(true)" class="mt-4 px-4 py-2 bg-emerald-600 rounded-xl text-white text-xs font-bold hover:bg-emerald-700 shadow-sm transition-colors border border-emerald-500">
                                        Force Reconnect
                                    </button>
                                    <p class="text-slate-400 text-sm max-w-sm" x-show="!peerOnline">
                                        Waiting for <span x-text="peerName"></span> to join the encrypted room...
                                    </p>
                                </div>

                                {{-- Picture-in-Picture Local Self-View (Top Right) --}}
                                <div class="absolute top-4 right-4 w-36 sm:w-48 aspect-video bg-slate-900/90 rounded-xl border border-slate-700/80 shadow-2xl overflow-hidden backdrop-blur-md z-20 group">
                                    <video
                                        id="localVideo"
                                        autoplay
                                        playsinline
                                        muted
                                        class="w-full h-full object-cover"
                                        :class="camOn ? 'block' : 'hidden'">
                                    </video>

                                    {{-- Camera Muted State --}}
                                    <div x-show="!camOn" class="w-full h-full flex flex-col items-center justify-center bg-slate-900 text-slate-400">
                                        <svg class="w-6 h-6 text-slate-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        <span class="text-[10px] font-semibold">Camera Paused</span>
                                    </div>

                                    {{-- Local Stream Badge --}}
                                    <div class="absolute bottom-1.5 left-2 right-2 flex items-center justify-between text-[10px] text-white/90 bg-slate-950/60 px-1.5 py-0.5 rounded backdrop-blur-sm">
                                        <span class="truncate font-medium">You (<span x-text="userRole"></span>)</span>
                                        <div class="flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="micOn ? 'bg-emerald-400' : 'bg-rose-500'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- 2. Pre-Call Waiting Room Stage --}}
                        <template x-if="!inCall">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-gradient-to-b from-slate-950 to-slate-900">
                                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4 shadow-lg shadow-emerald-950">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white mb-1">
                                    Cabinet Médical Dr. Mehdi Haniballi
                                </h2>
                                <p class="text-slate-400 text-xs md:text-sm max-w-md mb-6 leading-relaxed">
                                    Private, end-to-end encrypted telehealth room. Video and audio stream directly between doctor and patient using standard peer-to-peer WebRTC.
                                </p>

                                <div class="flex flex-wrap items-center justify-center gap-3 text-xs text-slate-300 bg-slate-900/80 px-4 py-2 rounded-xl border border-slate-800 mb-6">
                                    <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        STUN NAT Traversal Ready
                                    </span>
                                    <span class="text-slate-600">·</span>
                                    <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        AES-128 DTLS-SRTP
                                    </span>
                                    <span class="text-slate-600">·</span>
                                    <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Zero Third-Party SaaS
                                    </span>
                                </div>

                                <button
                                    @click="initSession()"
                                    class="px-8 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-xl shadow-emerald-950 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Connect Now</span>
                                </button>
                            </div>
                        </template>

                        {{-- 3. Top Floating Overlays (When in call) --}}
                        <template x-if="inCall">
                            <div class="relative z-10 flex items-center justify-between pointer-events-none">
                                <div class="flex items-center gap-2 pointer-events-auto">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-900/80 backdrop-blur-md text-emerald-400 border border-slate-700/60 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                        WebRTC P2P
                                    </span>
                                    <template x-if="usingSyntheticVideo">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-900/80 text-amber-200 border border-amber-700/60">
                                            Virtual Sensor Mode
                                        </span>
                                    </template>
                                </div>

                                {{-- Live Audio Visualizer Bar --}}
                                <div class="flex items-center gap-1 bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-700/60 pointer-events-auto">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                    <div class="w-12 h-2 bg-slate-800 rounded-full overflow-hidden flex items-center p-0.5">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-75" :style="'width: ' + audioLevel + '%'"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- 4. Bottom Floating Hardware Controls Bar --}}
                        <template x-if="inCall">
                            <div class="relative z-10 flex items-center justify-center gap-3 bg-slate-900/90 backdrop-blur-md py-2.5 px-6 rounded-2xl mx-auto border border-slate-700/80 shadow-2xl">
                                {{-- Mic Toggle --}}
                                <button
                                    @click="toggleMic()"
                                    :title="micOn ? 'Mute Microphone' : 'Unmute Microphone'"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center transition"
                                    :class="micOn ? 'bg-slate-800 hover:bg-slate-700 text-slate-200' : 'bg-rose-600 text-white shadow-lg shadow-rose-900/50'">
                                    <template x-if="micOn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                    </template>
                                    <template x-if="!micOn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                                    </template>
                                </button>

                                {{-- Camera Toggle --}}
                                <button
                                    @click="toggleCam()"
                                    :title="camOn ? 'Turn Off Camera' : 'Turn On Camera'"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center transition"
                                    :class="camOn ? 'bg-slate-800 hover:bg-slate-700 text-slate-200' : 'bg-rose-600 text-white shadow-lg shadow-rose-900/50'">
                                    <template x-if="camOn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </template>
                                    <template x-if="!camOn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </template>
                                </button>

                                {{-- Screen Sharing --}}
                                <button
                                    @click="toggleScreenShare()"
                                    :title="screenSharing ? 'Stop Screen Sharing' : 'Share Screen'"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center transition"
                                    :class="screenSharing ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' : 'bg-slate-800 hover:bg-slate-700 text-slate-200'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>

                                <div class="h-6 w-px bg-slate-800 mx-1"></div>

                                {{-- End Call Button --}}
                                <button
                                    @click="endCall()"
                                    class="h-10 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-lg shadow-rose-950 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"/></svg>
                                    <span>Leave</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Right Column: Clinical Workspace & In-Call Panels --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col justify-between h-full min-h-[500px]">
                    <div>
                        {{-- Workspace Tab Switcher --}}
                        <div class="flex items-center border-b border-slate-100 pb-3 mb-4 gap-2">
                            <button
                                @click="activeTab = 'notes'"
                                class="pb-2 text-xs font-bold transition px-2 flex items-center gap-1.5 border-b-2"
                                :class="activeTab === 'notes' ? 'text-emerald-800 border-emerald-700' : 'text-slate-500 border-transparent hover:text-slate-700'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Clinical Notes</span>
                            </button>

                            <button
                                @click="activeTab = 'metrics'"
                                class="pb-2 text-xs font-bold transition px-2 flex items-center gap-1.5 border-b-2"
                                :class="activeTab === 'metrics' ? 'text-emerald-800 border-emerald-700' : 'text-slate-500 border-transparent hover:text-slate-700'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Anthropometrics</span>
                            </button>

                            <button
                                @click="activeTab = 'chat'"
                                class="pb-2 text-xs font-bold transition px-2 flex items-center gap-1.5 border-b-2"
                                :class="activeTab === 'chat' ? 'text-emerald-800 border-emerald-700' : 'text-slate-500 border-transparent hover:text-slate-700'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>In-Room Chat</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            </button>
                        </div>

                        {{-- Tab 1: Clinical Notes (Doctor & Patient Records) --}}
                        <div x-show="activeTab === 'notes'" class="space-y-4">
                            <div class="bg-emerald-50/60 border border-emerald-200/70 rounded-xl p-3.5 space-y-1">
                                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Clinical Protocol Focus</span>
                                <p class="text-xs text-slate-700 leading-relaxed">
                                    Review dietary adherence, intermittent fasting tolerance, morning blood glucose curves, and functional joint decompression.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Live Consultation Directives</label>
                                    <template x-if="notesSaved">
                                        <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Saved to Record
                                        </span>
                                    </template>
                                </div>
                                <textarea
                                    x-model="clinicalNotes"
                                    rows="8"
                                    placeholder="Record clinical observations, dosage changes, macronutrient targets, and mobility homework..."
                                    class="w-full text-xs text-slate-800 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-emerald-600 focus:border-emerald-600 font-sans leading-relaxed"></textarea>
                            </div>

                            <button
                                @click="saveClinicalNotes()"
                                :disabled="notesSaving"
                                class="w-full py-2 bg-slate-900 hover:bg-emerald-800 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span x-text="notesSaving ? 'Persisting...' : 'Save Directives to Medical Record'"></span>
                            </button>
                        </div>

                        {{-- Tab 2: Patient Anthropometrics --}}
                        <div x-show="activeTab === 'metrics'" class="space-y-4">
                            @if ($appointment->patient)
                                <div class="grid grid-cols-2 gap-3 text-center">
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold">Recorded Weight</span>
                                        <p class="text-xl font-black text-slate-900 mt-0.5">{{ $appointment->patient->weight_kg ?? '68.5' }} kg</p>
                                        <span class="text-[10px] text-emerald-700 font-semibold">Active Baseline</span>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold">Stature Height</span>
                                        <p class="text-xl font-black text-slate-900 mt-0.5">{{ $appointment->patient->height_cm ?? '175' }} cm</p>
                                        <span class="text-[10px] text-slate-500 font-medium">Standard Measure</span>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Clinical Sensitivities & Allergies</span>
                                    <p class="text-xs text-slate-700 font-medium">
                                        {{ $appointment->patient->allergies ?: 'No declared food or pharmacological allergies.' }}
                                    </p>
                                </div>

                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Medical History Summary</span>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        {{ $appointment->patient->medical_notes ?: 'Patient is adhering to chronobiological intermittent fasting and lumbar decompression.' }}
                                    </p>
                                </div>
                            @else
                                <p class="text-xs text-slate-400">No patient profile attached to this appointment.</p>
                            @endif
                        </div>

                        {{-- Tab 3: In-Room Live Chat --}}
                        <div x-show="activeTab === 'chat'" class="space-y-3 flex-1 flex flex-col justify-between">
                            <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                <template x-for="(msg, idx) in chatMessages" :key="idx">
                                    <div :class="msg.isSystem ? 'text-center' : (msg.isMe ? 'text-right' : 'text-left')">
                                        <template x-if="msg.isSystem">
                                            <div class="inline-block px-2.5 py-1 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500 my-1" x-text="msg.text"></div>
                                        </template>
                                        <template x-if="!msg.isSystem">
                                            <div class="inline-block rounded-2xl px-3.5 py-2 text-xs max-w-[85%]" :class="msg.isMe ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200/60'">
                                                <div class="flex items-center gap-1.5 mb-0.5 text-[10px]" :class="msg.isMe ? 'text-emerald-200 justify-end' : 'text-slate-400'">
                                                    <span class="font-bold" x-text="msg.sender"></span>
                                                    <span x-text="msg.time"></span>
                                                </div>
                                                <p class="leading-relaxed" x-text="msg.text"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Chat Input Bar (Attached to bottom of panel) --}}
                    <div x-show="activeTab === 'chat'" class="mt-4 pt-3 border-t border-slate-100">
                        <form @submit.prevent="sendChatMessage()" class="flex items-center gap-2">
                            <input
                                type="text"
                                x-model="newMessage"
                                placeholder="Type a clinical message..."
                                class="flex-1 text-xs border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-600 focus:border-emerald-600 text-slate-800">
                            <button
                                type="submit"
                                class="px-3.5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-200">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-bold text-slate-800">Consultation Session Not Found</p>
                <a href="{{ route('appointments.index') }}" class="text-emerald-700 text-xs font-semibold hover:underline mt-2 inline-block">Return to appointments</a>
            </div>
        @endif
    </div>
</div>