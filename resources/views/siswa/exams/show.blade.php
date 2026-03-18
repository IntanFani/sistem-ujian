<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f1f5f9;
            user-select: none;
        }

        /* Header Responsive */
        .exam-header {
            background: #fff;
            border-bottom: 2px solid #10b981;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Card Soal */
        .question-card {
            border-radius: 20px;
            border: none;
            min-height: 400px;
            transition: 0.3s;
        }

        /* Tombol Nomor Soal */
        .number-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: bold;
            border: 2px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .number-box.active {
            background: #10b981 !important;
            color: #fff !important;
            border-color: #10b981 !important;
        }

        .number-box.filled {
            background: #3b82f6 !important;
            color: #fff !important;
            border-color: #3b82f6 !important;
        }

        /* Opsi Jawaban (Mobile Friendly) */
        .option-container {
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            background: #fff;
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
            transform: scale(1.2);
            margin-right: 12px;
        }

        .option-label {
            cursor: pointer;
            margin: 0;
            width: 100%;
            font-size: 1rem;
            line-height: 1.4;
            color: #334155;
        }

        /* --- BREAKPOINTS RESPONSIVE --- */
        @media (max-width: 768px) {

            /* Header jadi lebih ramping */
            .exam-header {
                padding: 8px 12px !important;
            }

            /* Judul Ujian dikecilkan */
            .exam-header h5 {
                font-size: 0.9rem !important;
                max-width: 150px;
                /* Biar judul panjang gak nabrak timer */
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .exam-header small {
                display: none;
            }

            /* Sembunyikan nama mapel di HP biar lega */

            /* Timer dikecilkan */
            .bg-dark.text-white.px-4 {
                padding-left: 12px !important;
                padding-right: 12px !important;
                padding-top: 5px !important;
                padding-bottom: 5px !important;
                border-radius: 10px !important;
            }

            #timer {
                font-size: 0.85rem !important;
            }

            .bi-clock-history {
                font-size: 0.8rem;
            }

            /* Tombol Selesai Ujian dibuat pas di jempol tapi ramping */
            .btn-danger.rounded-pill {
                padding: 6px 15px !important;
                font-size: 0.75rem !important;
            }
        }

        /* Tambahan buat layar sangat kecil (di bawah 400px) */
        @media (max-width: 400px) {
            .exam-header h5 {
                max-width: 100px;
            }

            .btn-danger.rounded-pill span {
                display: none;
            }

            /* Sembunyikan teks, sisa icon saja */
        }
    </style>
</head>

<body>

    <div class="exam-header shadow-sm p-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-rocket-takeoff-fill me-2"></i>{{ $exam->title }}
                </h5>
                <small class="text-muted">{{ $exam->subject->name }} | {{ Auth::user()->name }}</small>
            </div>
            <div class="text-center bg-dark text-white px-4 py-2 rounded-4 fw-bold shadow-sm">
                <i class="bi bi-clock-history me-2"></i> <span id="timer">--:--:--</span>
            </div>
            <button class="btn btn-danger rounded-pill px-4 fw-bold" onclick="finishExam()">
                <i class="bi bi-check-circle me-md-1"></i>
                <span class="d-none d-sm-inline">Selesai Ujian</span>
            </button>
        </div>
    </div>

    <div class="container-fluid py-4 mt-2">
        <div class="row g-3">
            <div class="col-lg-8 order-mobile-first">
                <div class="card question-card shadow-sm p-4">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-muted text-uppercase" style="letter-spacing: 1px;">Soal No <span
                                id="currentNumber">1</span></h6>
                    </div>

                    <div id="questionContent" class="fs-5 mb-4 text-dark lh-base">
                        Sedang memuat soal...
                    </div>

                    <div id="optionsList"></div>

                    <div class="d-flex justify-content-between mt-4 border-top pt-4 footer-nav">
                        <button class="btn btn-outline-secondary rounded-pill px-4 fw-bold" id="prevBtn">
                            <i class="bi bi-arrow-left me-1"></i> Prev
                        </button>
                        <button class="btn btn-success rounded-pill px-5 fw-bold" id="nextBtn">
                            Next <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 order-mobile-last">
                <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4">
                    <h6 class="fw-bold mb-3">Navigasi Soal</h6>
                    <div class="d-flex flex-wrap gap-2 justify-content-start">
                        @foreach ($exam->questions as $key => $q)
                            <div class="number-box" id="num-{{ $key + 1 }}"
                                onclick="jumpTo('{{ $key + 1 }}')">
                                {{ $key + 1 }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. Ambil data soal dari Laravel
        const questions = @json($exam->questions);
        let currentIndex = 0;

        const questionContent = document.getElementById('questionContent');
        const optionsList = document.getElementById('optionsList');
        const currentNumberLabel = document.getElementById('currentNumber');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        // 2. Fungsi Render Soal
        function renderQuestion(index) {
            const q = questions[index];
            currentNumberLabel.innerText = index + 1;
            questionContent.innerHTML = q.question_text;

            let optionsHtml = '';
            const labels = ['a', 'b', 'c', 'd', 'e'];

            labels.forEach(label => {
                // PERBAIKAN FINAL: Pakai 'opsi_' (Sesuai image_6.png)
                const optionText = q['opsi_' + label];

                if (optionText) {
                    optionsHtml += `
                    <div class="option-container" id="container-${label}" onclick="selectOption('${label}')">
                        <input class="form-check-input" type="radio" name="answer" 
                               id="opt-${label}" value="${label.toUpperCase()}">
                        <label class="option-label" for="opt-${label}">
                            <span class="fw-bold text-success">${label.toUpperCase()}.</span> ${optionText}
                        </label>
                    </div>
                `;
                }
            });
            optionsList.innerHTML = optionsHtml;

            updateNavStatus(index);

            // Atur tombol navigasi
            prevBtn.disabled = (index === 0);

            if (index === questions.length - 1) {
                nextBtn.innerHTML = 'Selesai Ujian <i class="bi bi-check-circle ms-2"></i>';
                nextBtn.classList.replace('btn-success', 'btn-danger');
            } else {
                nextBtn.innerHTML = 'Berikutnya <i class="bi bi-arrow-right ms-2"></i>';
                nextBtn.classList.replace('btn-danger', 'btn-success');
            }
        }

        // 3. Fungsi Pilih Jawaban
        function selectOption(label) {
            // Uncheck all & remove class
            document.querySelectorAll('.option-container').forEach(c => c.classList.remove('selected'));

            // Select input & add class
            const radio = document.getElementById('opt-' + label);
            if (radio) {
                radio.checked = true;
                document.getElementById('container-' + label).classList.add('selected');
            }
        }

        // 4. Navigasi
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

        function updateNavStatus(index) {
            document.querySelectorAll('.number-box').forEach(el => el.classList.remove('active'));
            const activeBox = document.getElementById('num-' + (index + 1));
            if (activeBox) activeBox.classList.add('active');
        }

        // 5. Timer Logic
        let timeLeft = {{ $exam->duration * 60 }};
        const timerDisplay = document.getElementById('timer');

        const timerInterval = setInterval(() => {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            timerDisplay.innerText =
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Waktu habis!');
                finishExam();
            }
            timeLeft--;
        }, 1000);

        function finishExam() {
            if (confirm("Apakah Anda yakin ingin mengakhiri ujian? Jawaban tidak bisa diubah lagi.")) {

                // Kirim permintaan ke server pake Fetch API (AJAX)
                fetch("{{ route('siswa.exams.finish', $exam->id) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Kalau sukses, lempar ke dashboard
                            window.location.href = "{{ route('siswa.dashboard') }}?status=success";
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Gagal mengakhiri ujian, coba cek koneksi internet!");
                    });
            }
        }

        // Jalankan Soal Pertama saat halaman dimuat
        if (questions.length > 0) {
            renderQuestion(currentIndex);
        } else {
            questionContent.innerHTML = "<div class='alert alert-warning'>Belum ada soal untuk ujian ini.</div>";
        }
    </script>
</body>

</html>
