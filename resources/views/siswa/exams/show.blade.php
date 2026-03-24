<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f5f9; user-select: none; }

        .exam-header {
            background: #fff;
            border-bottom: 2px solid #10b981;
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
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px; cursor: pointer; font-weight: bold;
            border: 2px solid #e2e8f0; background: #fff; color: #64748b;
            font-size: 0.9rem; transition: 0.2s;
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

        /* Opsi Jawaban */
        .option-container {
            border: 2px solid #f1f5f9; border-radius: 12px;
            padding: 12px 15px; margin-bottom: 10px; cursor: pointer;
            display: flex; align-items: center; background: #fff; transition: 0.2s;
        }

        .option-container:hover { border-color: #cbd5e1; background: #f8fafc; }
        .option-container.selected { background: #ecfdf5; border-color: #10b981; }
        .option-container input[type="radio"] { transform: scale(1.2); margin-right: 12px; }
        .option-label { cursor: pointer; margin: 0; width: 100%; color: #334155; }

        /* Responsive Scaling */
        @media (max-width: 768px) {
            .exam-header { padding: 8px 12px !important; }
            .exam-header h5 { 
                font-size: 0.9rem !important; max-width: 150px; 
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
            }
            .exam-header small { display: none; }
            .bg-dark.text-white.px-4 { padding: 5px 12px !important; border-radius: 10px !important; }
            #timer { font-size: 0.85rem !important; }
            .btn-danger.rounded-pill { padding: 6px 15px !important; font-size: 0.75rem !important; }
        }
    </style>
</head>

<body>

    <div class="exam-header shadow-sm p-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-rocket-takeoff-fill me-2"></i>{{ $exam->title }}</h5>
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
            <div class="col-lg-8">
                <div class="card question-card shadow-sm p-4">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-muted">SOAL NO <span id="currentNumber">1</span></h6>
                    </div>

                    <div id="questionContent" class="fs-5 mb-4 text-dark lh-base">Memuat soal...</div>
                    <div id="optionsList"></div>

                    <div class="d-flex justify-content-between mt-4 border-top pt-4">
                        <button class="btn btn-outline-secondary rounded-pill px-4 fw-bold" id="prevBtn">
                            <i class="bi bi-arrow-left me-1"></i> Prev
                        </button>
                        <button class="btn btn-success rounded-pill px-5 fw-bold" id="nextBtn">
                            Next <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h6 class="fw-bold mb-3">Navigasi Soal</h6>
                    <div class="d-flex flex-wrap gap-2">
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
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center mb-2 small">
                            <div class="number-box active me-2" style="width: 15px; height: 15px;"></div> <span>Belum diisi</span>
                        </div>
                        <div class="d-flex align-items-center small">
                            <div class="number-box filled me-2" style="width: 15px; height: 15px;"></div> <span>Dijawab</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                <span class="fw-bold text-success">${label.toUpperCase()}.</span> ${optionText}
                            </label>
                        </div>`;
                }
            });
            optionsList.innerHTML = optionsHtml;

            // Update warna navigasi (Hijau untuk aktif)
            document.querySelectorAll('.number-box').forEach(el => el.classList.remove('active'));
            document.getElementById('num-' + (index + 1)).classList.add('active');

            prevBtn.disabled = (index === 0);
            nextBtn.innerHTML = (index === questions.length - 1) ? 'Selesai Ujian <i class="bi bi-check-circle ms-2"></i>' : 'Next <i class="bi bi-arrow-right ms-2"></i>';
            nextBtn.className = (index === questions.length - 1) ? 'btn btn-danger rounded-pill px-5 fw-bold' : 'btn btn-success rounded-pill px-5 fw-bold';
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

        function jumpTo(number) { currentIndex = number - 1; renderQuestion(currentIndex); }
        
        nextBtn.addEventListener('click', () => {
            if (currentIndex < questions.length - 1) { currentIndex++; renderQuestion(currentIndex); } 
            else { finishExam(); }
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) { currentIndex--; renderQuestion(currentIndex); }
        });

        // Timer
        let timeLeft = {{ $exam->duration * 60 }};
        const timerInterval = setInterval(() => {
            const h = Math.floor(timeLeft / 3600);
            const m = Math.floor((timeLeft % 3600) / 60);
            const s = timeLeft % 60;
            document.getElementById('timer').innerText = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            if (timeLeft <= 0) { clearInterval(timerInterval); finishExam(); }
            timeLeft--;
        }, 1000);

        function finishExam() {
            if (confirm("Akhiri ujian? Jawaban tidak bisa diubah lagi.")) {
                fetch("{{ route('siswa.exams.finish', $exam->id) }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" }
                }).then(() => window.location.href = "{{ route('siswa.dashboard') }}");
            }
        }

        renderQuestion(currentIndex);
    </script>
</body>
</html>