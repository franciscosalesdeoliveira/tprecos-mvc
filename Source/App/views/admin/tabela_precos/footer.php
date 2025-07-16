<footer class="mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Atualização automática: <?= $config['atualizacao_auto'] ?> minutos
                            </small>
                        </div>
                        <div class="col-md-4 text-center">
                            <small class="text-muted">
                                Sistema de Tabela de Preços v1.0
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted">
                                Usuário: <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atalhos de teclado -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 bg-transparent">
                <div class="card-body py-1">
                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Atalhos:</strong>
                            <span class="badge bg-secondary mx-1">R</span> Recarregar página
                            <span class="badge bg-secondary mx-1">←→</span> Navegar carrossel
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações de status do sistema -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="text-center">
                <small class="text-muted">
                    Status: <span class="text-success">Online</span> |
                    Última sincronização: <span id="ultima-sync"><?= date('H:i:s') ?></span> |
                    Próxima atualização em: <span id="countdown"><?= $config['atualizacao_auto'] ?>:00</span>
                </small>
            </div>
        </div>
    </div>
</footer>

<script>
    // Contador regressivo para próxima atualização
    document.addEventListener('DOMContentLoaded', function() {
        iniciarContagemRegressiva();
    });

    function iniciarContagemRegressiva() {
        const countdownElement = document.getElementById('countdown');
        if (!countdownElement) return;

        let tempoRestante = <?= $config['atualizacao_auto'] ?> * 60; // converter para segundos

        const interval = setInterval(() => {
            const minutos = Math.floor(tempoRestante / 60);
            const segundos = tempoRestante % 60;

            countdownElement.textContent =
                `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;

            tempoRestante--;

            if (tempoRestante < 0) {
                clearInterval(interval);
                // A página será recarregada automaticamente pelo meta refresh
                countdownElement.textContent = '00:00';
            }
        }, 1000);
    }

    // Atualizar hora da última sincronização
    function atualizarUltimaSync() {
        const ultimaSyncElement = document.getElementById('ultima-sync');
        if (ultimaSyncElement) {
            const agora = new Date();
            ultimaSyncElement.textContent = agora.toLocaleTimeString('pt-BR');
        }
    }

    // Atualizar a cada minuto
    setInterval(atualizarUltimaSync, 60000);
</script>