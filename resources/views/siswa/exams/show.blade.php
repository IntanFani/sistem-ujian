<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }} | CBT MTs Al Huda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    {{-- Tambahkan SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            user-select: none; /* Mencegah siswa copy-paste teks soal */
            font-family: 'Inter', sans-serif;
        }

        .exam-header {
            background: #ffffff;
            border-bottom: 3px solid #10b981; /* Garis hijau khas CBT */
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .question-card {
            border-radius: 20px;
            border: none;
            min-height: 400px;
        }

        /* Navigasi Nomor */
        .number-box {
            width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; cursor: pointer; font-weight: 700;
            border: 2px solid #e2e8f0; background: #ffffff; color: #64748b;
            font-size: 0.95rem; transition: all 0.2s ease;
        }

        .number-box:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        /* Nomor yang sedang dibuka */
        .number-box.active {
            background: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        /* Nomor yang sudah dijawab */
        .number-box.filled {
            background: #3b82f6 !important; /* Warna biru untuk soal yg sudah dijawab */
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }

        /* Opsi Jawaban */
        .option-container {
            border: 2px solid #f1f5f9; border-radius: 14px;
            padding: 14px 18px; margin-bottom: 12px; cursor: pointer;
            display: flex; align-items: center; background: #ffffff; transition: all 0.2s ease;
        }

        .option-container:hover { 
            border-color: #cbd5e1; 
            background: #f8fafc; 
        }
        
        .option-container.selected { 
            background: #ecfdf5; 
            border-color: #10b981; 
        }

        .option-container input[type="radio"] { 
            transform: scale(1.3); 
            margin-right: 15px; 
            cursor: pointer;
        }

        .option-label { 
            cursor: pointer; 
            margin: 0; 
            width: 100%; 
            color: #334155; 
            font-size: 1.05rem;
        }

        /* Responsive Scaling */
        @media (max-width: 768px) {
            .exam-header { padding: 10px !important; }
            .header-title-wrapper h5 { font-size: 1rem !important; }
            .header-title-wrapper small { display: none; }
            .timer-box { padding: 6px 12px !important; font-size: 0.9rem !important; }
            .btn-finish-exam { padding: 6px 16px !important; font-size: 0.85rem !important; }
            .school-logo { height: 32px !important; }
        }
    </style>
</head>

<body>

    {{-- HEADER UJIAN --}}
    <div class="exam-header shadow-sm py-3 px-4">
        <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
            
            {{-- Kiri: Logo & Info Ujian --}}
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo MTs Al Huda" class="school-logo me-3" style="height: 45px; width: auto; object-fit: contain;">
                <div class="header-title-wrapper">
                    <h5 class="fw-bold mb-1 text-success lh-1">{{ $exam->title }}</h5>
                    <small class="text-muted fw-medium"><i class="bi bi-person-fill me-1"></i>{{ Auth::user()->name }} <span class="mx-1">|</span> {{ $exam->subject->name }}</small>
                </div>
            </div>

            {{-- Kanan: Timer & Tombol Selesai --}}
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <div class="timer-box text-center bg-dark text-white px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center">
                    <i class="bi bi-alarm text-warning me-2 fs-5 lh-1"></i> 
                    <span id="timer" class="lh-1" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 1px;">--:--:--</span>
                </div>
                <button class="btn btn-danger btn-finish-exam rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="finishExam()">
                    <i class="bi bi-stop-circle-fill me-md-1"></i>
                    <span class="d-none d-sm-inline">Akhiri Ujian</span>
                </button>
            </div>

        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="container-fluid py-4">
        <div class="row g-4">
            
            {{-- Bagian Soal (Kiri) --}}
            <div class="col-lg-8">
                <div class="card question-card shadow-sm p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold mb-0 text-dark">
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill me-2">Soal No. <span id="currentNumber">1</span></span>
                        </h5>
                    </div>

                    {{-- Area Teks Soal --}}
                    <div id="questionContent" class="fs-5 mb-4 text-dark" style="line-height: 1.7;">Memuat soal...</div>
                    
                    {{-- Area Pilihan Ganda --}}
                    <div id="optionsList"></div>

                    {{-- Tombol Navigasi Bawah --}}
                    <div class="d-flex justify-content-between mt-5 border-top pt-4">
                        <button class="btn btn-light border border-2 rounded-pill px-4 py-2 fw-bold text-secondary transition-3d" id="prevBtn">
                            <i class="bi bi-arrow-left-circle me-1"></i> Sebelumnya
                        </button>
                        <button class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm transition-3d" id="nextBtn">
                            Selanjutnya <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bagian Navigasi Nomor (Kanan) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 100px;">
                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="bi bi-grid-3x3-gap-fill text-success me-2"></i> Navigasi Soal
                    </h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($exam->questions as $key => $q)
                            @php
                                $isFilled = $session->answers->where('question_id', $q->id)->first();
                            @endphp
                            <div class="number-box {{ $isFilled ? 'filled' : '' }}" 
                                 id="num-{{ $key + 1 }}" onclick="jumpTo('{{ $key + 1 }}')">
                                {{ $key + 1 }}
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Keterangan Warna --}}
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex align-items-center mb-2 small text-muted">
                            <div class="rounded-circle border border-2 bg-white me-2" style="width: 14px; height: 14px;"></div> <span>Belum dijawab</span>
                        </div>
                        <div class="d-flex align-items-center mb-2 small text-muted">
                            <div class="rounded-circle bg-primary me-2" style="width: 14px; height: 14px;"></div> <span>Sudah dijawab</span>
                        </div>
                        <div class="d-flex align-items-center small text-muted">
                            <div class="rounded-circle bg-success me-2" style="width: 14px; height: 14px;"></div> <span>Sedang dibuka</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Tambahkan Script SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Data dari Laravel
        const questions = @json($exam->questions);
        // Load jawaban yang sudah ada di DB (pluck: ID Soal => Jawaban)
        let userAnswers = @json($session->answers->pluck('answer', 'question_id'));
        let currentIndex = 0;

        const questionContent = document.getElementById('questionContent');
        const optionsList = document.getElementById('optionsList');
        const currentNumberLabel = document.getElementById('currentNumber');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        function renderQuestion(index) {
            const q = questions[index];
            currentNumberLabel.innerText = index + 1;
            questionContent.innerHTML = q.question_text;

            let optionsHtml = '';
            const labels = ['a', 'b', 'c', 'd', 'e'];
            const savedAnswer = userAnswers[q.id] ? userAnswers[q.id].toLowerCase() : null;

            labels.forEach(label => {
                const optionText = q['opsi_' + label];
                if (optionText) {
                    const isChecked = (savedAnswer === label) ? 'checked' : '';
                    const isSelectedClass = (savedAnswer === label) ? 'selected' : '';

                    optionsHtml += `
                        <div class="option-container ${isSelectedClass}" id="container-${label}" onclick="selectOption('${label}', ${q.id})">
                            <input class="form-check-input" type="radio" name="answer" id="opt-${label}" value="${label.toUpperCase()}" ${isChecked}>
                            <label class="option-label" for="opt-${label}">
                                <span class="fw-bold text-success me-2">${label.toUpperCase()}.</span> ${optionText}
                            </label>
                        </div>`;
                }
            });
            optionsList.innerHTML = optionsHtml;

            // Update warna navigasi (Hijau untuk aktif)
            document.querySelectorAll('.number-box').forEach(el => el.classList.remove('active'));
            document.getElementById('num-' + (index + 1)).classList.add('active');

            prevBtn.disabled = (index === 0);
            
            // Ubah tombol Next menjadi Akhiri Ujian di soal terakhir
            if (index === questions.length - 1) {
                nextBtn.innerHTML = '<i class="bi bi-stop-circle-fill me-1"></i> Akhiri Ujian';
                nextBtn.className = 'btn btn-danger rounded-pill px-5 py-2 fw-bold shadow-sm transition-3d';
            } else {
                nextBtn.innerHTML = 'Selanjutnya <i class="bi bi-arrow-right-circle ms-1"></i>';
                nextBtn.className = 'btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm transition-3d';
            }
        }

        function selectOption(label, questionId) {
            // Update UI
            document.querySelectorAll('.option-container').forEach(c => c.classList.remove('selected'));
            document.getElementById('opt-' + label).checked = true;
            document.getElementById('container-' + label).classList.add('selected');
            
            // Tandai kotak nomor jadi Biru
            document.getElementById('num-' + (currentIndex + 1)).classList.add('filled');
            userAnswers[questionId] = label.toUpperCase();

            // Auto-Save via AJAX
            fetch("{{ route('siswa.exams.save-answer') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ question_id: questionId, answer: label.toUpperCase() })
            });
        }

        function jumpTo(number) { 
            currentIndex = number - 1; 
            renderQuestion(currentIndex); 
        }
        
        nextBtn.addEventListener('click', () => {
            if (currentIndex < questions.length - 1) { 
                currentIndex++; 
                renderQuestion(currentIndex); 
            } else { 
                finishExam(); 
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) { 
                currentIndex--; 
                renderQuestion(currentIndex); 
            }
        });

        // Timer
        let timeLeft = {{ $exam->duration * 60 }};
        const timerInterval = setInterval(() => {
            const h = Math.floor(timeLeft / 3600);
            const m = Math.floor((timeLeft % 3600) / 60);
            const s = timeLeft % 60;
            document.getElementById('timer').innerText = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            if (timeLeft <= 0) { 
                clearInterval(timerInterval); 
                
                // Auto finish saat waktu habis pakai SweetAlert
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Ujian otomatis diakhiri.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    executeFinish();
                });
            }
            timeLeft--;
        }, 1000);

        // Fungsi Konfirmasi Akhiri Ujian menggunakan SweetAlert2
        function finishExam() {
            Swal.fire({
                title: 'Akhiri Ujian?',
                text: "Pastikan semua soal telah dijawab. Anda tidak bisa kembali setelah ujian diakhiri!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Akhiri Ujian!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeFinish();
                }
            });
        }

        // Fungsi Eksekusi Akhiri Ujian ke Server
        function executeFinish() {
            Swal.fire({
                title: 'Menyimpan Ujian...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('siswa.exams.finish', $exam->id) }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" }
            }).then(() => window.location.href = "{{ route('siswa.dashboard') }}");
        }

        // Render soal pertama saat halaman dimuat
        renderQuestion(currentIndex);
    </script>
</body>
</html>