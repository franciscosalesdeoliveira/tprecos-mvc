<?php $this->layout("_theme", ["title" =>  $title]); ?>

<?php $this->start("conteudo"); ?>
<link rel="stylesheet" href=<?= url("public/assets/css/excel.css"); ?>>
<div class="wrapper">
    <main>
        <div class="main-content">
            <div class="excel-container">
                <div class="excel-header">
                    <h1 class="excel-title">Importar Arquivo CSV</h1>
                    <p class="excel-subtitle text-white">
                        <i class="fas fa-file-csv" style="font-size: 24px; margin-right: 8px;"></i>
                        Selecione o arquivo CSV para importar
                    </p>
                    <div class="excel-wave">
                        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                        </svg>
                    </div>
                </div>
                <div class="excel-content">
                    <div class="excel-options">
                        <form method="post" action="<?= url("admin/excel/importar"); ?>" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="arquivo">Arquivo CSV</label>
                                <div class="file-input-container" id="dropZone">
                                    <i class="fas fa-cloud-upload-alt file-input-icon"></i>
                                    <div class="file-input-text">Arraste e solte seu arquivo CSV aqui</div>
                                    <div class="file-input-subtext">ou clique para selecionar</div>
                                    <input type="file" name="arquivo" id="arquivo" accept=".csv" required>
                                </div>
                                <div class="file-name" id="fileName">
                                    <span class="file-name-text" id="fileNameText"></span>
                                    <i class="fas fa-times remove-file" id="removeFile"></i>
                                </div>
                                <div class="help-text">Formatos aceitos: .csv (máximo 10MB)</div>
                            </div>

                            <div class="form-group">
                                <label for="grupo_selecionado">Selecione o Grupo (opcional)</label>
                                <select name="grupo_selecionado" id="grupo_selecionado" class="form-control">
                                    <option value="">-- Usar grupo do CSV --</option>
                                    <?php foreach ($grupos as $grupo): ?>
                                        <option value="<?= $grupo['id']; ?>"><?= htmlspecialchars($grupo['nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Selecione um grupo para sobrescrever o grupo indicado no arquivo</div>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn" id="submitBtn">
                                    <i class="fas fa-upload"></i>
                                    Importar Arquivo
                                </button>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Adicionando Bootstrap JS e Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Script para melhorar a experiência do usuário
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('arquivo');
        const fileName = document.getElementById('fileName');
        const fileNameText = document.getElementById('fileNameText');
        const removeFile = document.getElementById('removeFile');
        const submitBtn = document.getElementById('submitBtn');
        const uploadForm = document.getElementById('uploadForm');

        // Atualizar interface quando arquivo for selecionado
        fileInput.addEventListener('change', function() {
            updateFileInfo();
        });

        // Remover arquivo
        removeFile.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.value = '';
            fileName.style.display = 'none';
            dropZone.classList.remove('active');
            submitBtn.disabled = true;
        });

        // Efeito de arrastar e soltar
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('active');
        }

        function unhighlight() {
            dropZone.classList.remove('active');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateFileInfo();
        }

        // Atualizar informações do arquivo
        function updateFileInfo() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                // Verificar se é um arquivo CSV
                if (!file.name.toLowerCase().endsWith('.csv')) {
                    alert('Por favor, selecione um arquivo CSV válido.');
                    fileInput.value = '';
                    return;
                }

                // Exibir nome do arquivo
                fileNameText.textContent = file.name;
                fileName.style.display = 'flex';
                dropZone.classList.add('active');
                submitBtn.disabled = false;
            } else {
                fileName.style.display = 'none';
                dropZone.classList.remove('active');
                submitBtn.disabled = true;
            }
        }

        // Adicionar evento de submit ao formulário
        uploadForm.addEventListener('submit', function(e) {
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Por favor, selecione um arquivo CSV para importar.');
                return false;
            }

            // Verificar se o arquivo é um CSV
            if (!fileInput.files[0].name.toLowerCase().endsWith('.csv')) {
                e.preventDefault();
                alert('Por favor, selecione um arquivo CSV válido.');
                return false;
            }

            // Se tudo estiver correto, exibir mensagem de espera
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
            submitBtn.disabled = true;

            // Permitir que o formulário seja enviado naturalmente
            return true;
        });

        // Inicializar estado do botão
        submitBtn.disabled = !fileInput.value;

        // Adicionar efeito ao botão de voltar
        const btnBack = document.querySelector('.btn-back');
        if (btnBack) {
            btnBack.addEventListener('mouseenter', function() {
                this.querySelector('i').style.transform = 'translateX(-3px)';
                this.querySelector('i').style.transition = 'transform 0.3s';
            });

            btnBack.addEventListener('mouseleave', function() {
                this.querySelector('i').style.transform = 'translateX(0)';
            });
        }
    });
</script>
<?php $this->stop(); ?>