<?php $this->layout("_theme"); ?>

<?php $this->start("conteudo"); ?>
<div class="main-content">
    <div class="config-container mt-5">
        <div class="config-header">
            <h1 class="config-title">Configurações da Tabela de Preços</h1>
            <p class="config-subtitle">Ajuste as configurações da tabela de preços</p>
            <div class="config-wave">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>
        </div>

        <div class="row justify-content-center config-content">
            <div class="col-md-8 config-form-container">
                <!-- <div class="card shadow"> -->
                <div class="card-body">
                    <!-- Formulário Unificado -->
                    <form id="formConfiguracoes" action="<?= url("admin/tabela-precos"); ?>" method="GET" target="_blank">
                        <!-- Seleção de Grupo -->
                        <div class="mb-3">
                            <label for="grupo" class="form-label fw-bold">Grupo a ser exibido:</label>
                            <select class="form-select" id="grupo" name="grupo">
                                <?php foreach ($grupos as $id => $nome): ?>
                                    <option value="<?= $id ?>" <?= ($grupo_selecionado == $id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Escolha um grupo específico ou todos os grupos.</div>
                        </div>

                        <!-- Limite de Itens -->
                        <div class="mb-3">
                            <label for="limite" class="form-label fw-bold">Quantidade de itens por grupo:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="limite" name="limite"
                                    min="1" value="<?php echo $limite; ?>" placeholder="Ex: 10" required>
                                <span class="input-group-text">itens</span>
                            </div>
                            <div class="form-text">Defina quantos itens serão exibidos em cada grupo.</div>
                        </div>

                        <!-- Tempo por Slide -->
                        <div class="mb-3">
                            <label for="tempo" class="form-label fw-bold">Tempo por slide (segundos):</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tempo" name="tempo"
                                    min="1" value="<?php echo $tempo; ?>" placeholder="Ex: 60" required>
                                <span class="input-group-text">segundos</span>
                            </div>
                            <div class="form-text">Defina o tempo em segundos que cada slide ficará visível.</div>
                        </div>

                        <!-- Controle de Propagandas-->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Exibição de Propagandas:</label>
                            <!-- Importante: Alteramos para usar um campo oculto que sempre será enviado -->
                            <input type="hidden" name="propagandas_ativas" value="0">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="propagandas_ativas" name="propagandas_ativas" value="1"
                                    <?= $propagandas_ativas ? 'checked' : '' ?>>
                                <label class="form-check-label" for="propagandas_ativas">Ativar exibição de propagandas</label>
                            </div>
                            <div class="mt-2">
                                <label for="tempo_propagandas" class="form-label">Tempo de exibição (segundos):</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="tempo_propagandas" name="tempo_propagandas"
                                        min="1" value="<?= $tempo_propagandas; ?>" placeholder="Ex: 5"
                                        <?= $propagandas_ativas ? '' : 'disabled' ?>>
                                    <span class="input-group-text">segundos</span>
                                </div>
                            </div>

                            <div class="form-text">Defina se as propagandas serão exibidas e por quanto tempo.</div>
                        </div>

                        <!-- Tempo de Atualização Automática -->
                        <div class="mb-3">
                            <label for="atualizacao_auto" class="form-label fw-bold">Atualização automática:</label>
                            <select class="form-select" id="atualizacao_auto" name="atualizacao_auto">
                                <?php foreach ($opcoes_atualizacao as $valor => $texto): ?>
                                    <option value="<?= $valor ?>" <?= ($atualizacao_auto == $valor) ? 'selected' : '' ?>>
                                        <?= $texto ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Defina com que frequência a tabela será atualizada automaticamente.</div>
                        </div>

                        <!-- Seleção de Tema -->
                        <div class="mb-4">
                            <label for="tema" class="form-label fw-bold">Tema visual:</label>
                            <select class="form-select" id="tema" name="tema">
                                <?php foreach ($temas as $valor => $nome): ?>
                                    <option value="<?= $valor ?>" <?= ($tema == $valor) ? 'selected' : '' ?>>
                                        <?= $nome ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Escolha o estilo visual para a tabela de preços.</div>
                        </div>

                        <!-- Botão de Visualização -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-eye"></i> Visualizar Tabela
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h5 class="border-bottom pb-2">Pré-visualização dos temas</h5>
                        <div class="row mt-3">
                            <?php foreach ($temas as $valor => $nome):
                                // Define as cores do tema para a prévia
                                $corFundo = $valor == 'padrao' ? 'bg-primary' : ($valor == 'supermercado' ? 'bg-success' : 'bg-warning');
                                $corTexto = $valor == 'padaria' ? 'text-dark' : 'text-white';
                            ?>
                                <div class="col-md-4 mb-2">
                                    <div class="card border h-100"> <!-- Adicionando h-100 para igualar altura -->
                                        <div class="card-header h-100 <?= $corFundo ?> <?= $corTexto ?> text-center">
                                            <?= $nome ?>
                                        </div>
                                        <div class="card-body p-2 text-center" style="font-size: 0.8rem;">
                                            <span class="badge <?= $corFundo ?> <?= $corTexto ?> d-block mb-1">Amostra</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Botões adicionais -->
                    <!-- arrumar a propaganda aqui -->
                  
                </div>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação do formulário
        document.getElementById('formConfiguracoes').addEventListener('submit', function(event) {
            const limite = document.getElementById('limite').value;
            const tempo = document.getElementById('tempo').value;
            const propagandasAtivas = document.getElementById('propagandas_ativas').checked;
            const tempoPropagandas = document.getElementById('tempo_propagandas').value;

            if (!limite || parseInt(limite) <= 0) {
                event.preventDefault();
                alert('Por favor, insira um número válido de itens maior que zero.');
                document.getElementById('limite').focus();
                return false;
            }

            if (!tempo || parseInt(tempo) <= 0) {
                event.preventDefault();
                alert('Por favor, insira um tempo válido em segundos maior que zero.');
                document.getElementById('tempo').focus();
                return false;
            }

            if (propagandasAtivas && (!tempoPropagandas || parseInt(tempoPropagandas) <= 0)) {
                event.preventDefault();
                alert('Por favor, insira um tempo válido para as propagandas em segundos maior que zero.');
                document.getElementById('tempo_propagandas').focus();
                return false;
            }

            // Se tudo estiver correto, o formulário será enviado normalmente
            return true;
        });

        // Habilitar/desabilitar campo de tempo de propagandas
        document.getElementById('propagandas_ativas').addEventListener('change', function() {
            document.getElementById('tempo_propagandas').disabled = !this.checked;
        });

        // Visualização rápida do tema selecionado
        document.getElementById('tema').addEventListener('change', function() {
            const temaAtual = this.value;
            const exemplos = document.querySelectorAll('.card-header');

            exemplos.forEach(function(exemplo) {
                exemplo.classList.remove('bg-primary', 'bg-success', 'bg-warning', 'text-white', 'text-dark');

                if (temaAtual === 'padrao') {
                    exemplo.classList.add('bg-primary', 'text-white');
                } else if (temaAtual === 'supermercado') {
                    exemplo.classList.add('bg-success', 'text-white');
                } else if (temaAtual === 'padaria') {
                    exemplo.classList.add('bg-warning', 'text-dark');
                } else if (temaAtual === 'informatica') {
                    exemplo.classList.add('bg-secondary', 'text-white');
                }
            });
        });
    });
</script>
<?php $this->stop(); ?>