<?php
// View: views/tabelaprecos/index.php
$estiloAtual = $config['estiloAtual'];
$tempoSlide = $config['tempoSlide'];
$tempoExtraPorProduto = $config['tempoExtraPorProduto'];
$tempoRolagem = $config['tempoRolagem'];
$atualizacao_auto = $config['atualizacao_auto'];
$tempo_propagandas = $config['tempo_propagandas'];
$propagandas_ativas = $config['propagandas_ativas'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/v6-shims.min.css">

    <style>
        .preco-novo {
            background-color: #28a745 !important;
            color: white !important;
        }

        .preco-destaque {
            font-size: 1.5rem !important;
            font-weight: bold !important;
            color: #fff !important;
        }

        .tabela-container {
            position: relative;
            overflow: hidden;
            height: 80vh;
        }

        .tabela-scroll {
            position: relative;
            transition: top 0.5s ease-in-out;
        }

        .propaganda-item {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .propaganda-imagem {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .propaganda-titulo {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 80%;
        }

        .grupo-header {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 20px;
            z-index: 1000;
        }

        .carousel-item {
            height: 100vh;
            padding-bottom: 60px;
        }

        body {
            padding-bottom: 80px;
        }
    </style>
</head>

<body class="<?= $estiloAtual['background'] ?> <?= $estiloAtual['text'] ?>">
    <div class="container-fluid p-2">
        <?php if (!empty($grupos)): ?>
            <?php
            $slideIndex = 0;
            $primeiro = true;
            $index = 0;
            ?>

            <?php if ($mostrarCarrossel): ?>
                <div id="grupoCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                    <?php endif; ?>

                    <?php
                    $totalGrupos = count($grupos);
                    $grupoAtual = 0;
                    ?>

                    <?php foreach ($grupos as $nomeGrupo => $listaProdutos): ?>
                        <?php
                        $numProdutos = count($listaProdutos);
                        $tempoGrupo = $tempoSlide + ($numProdutos * $tempoExtraPorProduto);
                        $tempoBaseRolagem = $tempoRolagem * 1000;
                        $modoExibicao = $numProdutos > 10 ? 'grande' : 'normal';
                        ?>

                        <?php if ($mostrarCarrossel): ?>
                            <div class="carousel-item <?= $primeiro ? 'active' : '' ?>"
                                data-bs-interval="<?= $modoExibicao == 'grande' ? max($tempoGrupo, $tempoBaseRolagem) : $tempoGrupo ?>">
                            <?php endif; ?>

                            <!-- Cabeçalho do grupo -->
                            <div class="grupo-header <?= $estiloAtual['header_bg'] ?> <?= $estiloAtual['text'] ?>">
                                <h2 class="text-center fs-1 fw-bold"><?= htmlspecialchars($nomeGrupo) ?></h2>
                            </div>

                            <!-- Container da tabela -->
                            <?php if ($modoExibicao != 'grande'): ?>
                                <div class="table-container mx-auto" style="max-width: 100%;">
                                <?php endif; ?>

                                <div id="tabela-container-<?= $index ?>" class="tabela-container tabela-grande">
                                    <div id="tabela-scroll-<?= $index ?>" class="tabela-scroll"
                                        data-total-produtos="<?= $numProdutos ?>"
                                        data-tempo-rolagem="<?= $tempoBaseRolagem ?>">

                                        <table class="table table-striped table-hover border">
                                            <thead class="<?= $estiloAtual['table_header'] ?>">
                                                <tr>
                                                    <th class="text-center fs-2" style="width: 70%;">Produto</th>
                                                    <th class="text-center fs-2" style="width: 30%;">Preço</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($listaProdutos as $item): ?>
                                                    <?php
                                                    $recemAtualizado = false;
                                                    if (isset($item['ultima_atualizacao']) && !empty($item['ultima_atualizacao'])) {
                                                        $dataAtualizacao = new DateTime($item['ultima_atualizacao']);
                                                        $agora = new DateTime();
                                                        $diferenca = $agora->diff($dataAtualizacao);
                                                        $recemAtualizado = $diferenca->days < 1;
                                                    }
                                                    ?>
                                                    <tr<?= $recemAtualizado ? ' class="preco-novo"' : '' ?>>
                                                        <td class="text-center fs-4"><?= htmlspecialchars($item['produto']) ?></td>
                                                        <td class="text-center <?= $recemAtualizado ? 'preco-destaque' : 'fs-4 fw-bold' ?>">
                                                            R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                                        </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <?php if ($modoExibicao != 'grande'): ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($mostrarCarrossel): ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $primeiro = false;
                        $index++;
                        $slideIndex++;
                        $grupoAtual++;
                        ?>

                        <?php
                        // Inserir propaganda após alguns grupos
                        if ($propagandas_ativas && !empty($propagandas)) {
                            $deveExibirPropaganda = ($grupoAtual > 0 && $grupoAtual % 2 === 0);

                            if ($deveExibirPropaganda) {
                                $indicePropaganda = ($grupoAtual / floor($totalGrupos / count($propagandas)) - 1) % count($propagandas);
                                $propaganda = $propagandas[intval($indicePropaganda)];
                        ?>
                                <div class="carousel-item" data-bs-interval="<?= $tempo_propagandas ?>">
                                    <div class="propaganda-item">
                                        <div class="position-relative">
                                            <img src="/<?= htmlspecialchars($propaganda['imagem']) ?>"
                                                class="propaganda-imagem"
                                                alt="<?= htmlspecialchars($propaganda['titulo']) ?>">

                                            <?php if (!empty($propaganda['titulo']) || !empty($propaganda['descricao'])): ?>
                                                <div class="propaganda-titulo">
                                                    <?php if (!empty($propaganda['titulo'])): ?>
                                                        <h3><?= htmlspecialchars($propaganda['titulo']) ?></h3>
                                                    <?php endif; ?>
                                                    <?php if (!empty($propaganda['descricao'])): ?>
                                                        <p class="mb-0"><?= htmlspecialchars($propaganda['descricao']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                        <?php
                                $slideIndex++;
                            }
                        }
                        ?>

                    <?php endforeach; ?>

                    <?php if ($mostrarCarrossel): ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning m-5">
                Nenhum produto disponível para exibição.
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer <?= $estiloAtual['header_bg'] ?> <?= $estiloAtual['text'] ?>">
        <div class="d-flex justify-content-between align-items-center" style="max-width: 100%;">
            <div><?= date('d/m/Y') ?></div>
            <div id="relogio"><?= date('H:i:s') ?></div>
            <div id="hora-atualizacao">
                Atualizado às <span id="hora-local"></span>
                <?php if ($atualizacao_auto > 0): ?>
                    <span id="proxima-atualizacao" class="ms-2">
                        (Próxima: <span id="tempo-restante"><?= $atualizacao_auto ?></span> min)
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <?php if (!$isAjax): ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Configurações JavaScript
            const atualizacaoAuto = <?= $atualizacao_auto ?>;
            const tempoSlide = <?= $tempoSlide ?>;
            const tempoRolagem = <?= $tempoRolagem ?>;
            let tempoRestante = atualizacaoAuto * 60;
            let intervalAtualizacao = null;

            document.addEventListener('DOMContentLoaded', function() {
                initializeCarousel();
                initializeClock();
                setupAutoScroll();
                atualizarHoraLocal();

                if (atualizacaoAuto > 0) {
                    iniciarContagemRegressiva();
                }

                precarregarImagensPropagandas();
                setupKeyboardNavigation();
            });

            function initializeCarousel() {
                setTimeout(function() {
                    try {
                        const carouselElement = document.getElementById('grupoCarousel');
                        if (carouselElement) {
                            const activeItem = document.querySelector('.carousel-item.active');
                            const initialInterval = activeItem ?
                                parseInt(activeItem.dataset.bsInterval) : tempoSlide;

                            const carousel = new bootstrap.Carousel(carouselElement, {
                                interval: initialInterval,
                                ride: 'carousel',
                                wrap: true
                            });

                            setupCarouselClickNavigation(carousel, carouselElement);
                            setupCarouselEvents(carousel, carouselElement);
                        }
                    } catch (e) {
                        console.error("Erro ao inicializar o carrossel:", e);
                    }
                }, 500);
            }

            function setupCarouselClickNavigation(carousel, carouselElement) {
                carouselElement.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.closest('a')) return;

                    const rect = this.getBoundingClientRect();
                    const clickPosition = (e.clientX - rect.left) / rect.width;

                    if (clickPosition < 0.3) {
                        carousel.prev();
                    } else if (clickPosition > 0.7) {
                        carousel.next();
                    } else {
                        carousel.next();
                    }

                    carousel._config.interval = false;
                    setTimeout(() => {
                        const activeItem = document.querySelector('.carousel-item.active');
                        const interval = activeItem ? parseInt(activeItem.dataset.bsInterval) : tempoSlide;
                        carousel._config.interval = interval;
                        carousel._cycle();
                    }, 100);
                });
            }

            function setupCarouselEvents(carousel, carouselElement) {
                carouselElement.addEventListener('slide.bs.carousel', function(e) {
                    resetAllScrolls();

                    const proximoSlide = e.relatedTarget;
                    const intervaloProximo = proximoSlide ? parseInt(proximoSlide.dataset.bsInterval) : tempoSlide;
                    console.log("Mudando para slide com intervalo:", intervaloProximo, "ms");
                });
            }

            function initializeClock() {
                setInterval(function() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('pt-BR');
                    document.getElementById('relogio').innerText = timeString;
                }, 1000);
            }

            function setupAutoScroll() {
                const scrollContainers = document.querySelectorAll('.tabela-scroll');

                scrollContainers.forEach(container => {
                    const containerParent = container.parentElement;

                    if (container.offsetHeight > containerParent.offsetHeight) {
                        const totalProdutos = parseInt(container.dataset.totalProdutos || 0);
                        const tempoRolagem = parseInt(container.dataset.tempoRolagem || 20000);
                        const totalHeight = container.offsetHeight - containerParent.offsetHeight;

                        let scrollStep = 1;
                        if (totalProdutos > 30) {
                            scrollStep = Math.max(1, Math.floor(totalHeight / (tempoRolagem / 30)));
                        } else {
                            scrollStep = Math.max(1, Math.floor(totalHeight / (tempoRolagem / 50)));
                        }

                        container.scrollData = {
                            currentPosition: 0,
                            maxScroll: totalHeight,
                            step: scrollStep,
                            interval: null,
                            totalProdutos: totalProdutos
                        };

                        startScroll(container);
                    }
                });
            }

            function startScroll(container) {
                if (container.scrollData && container.scrollData.interval) {
                    clearInterval(container.scrollData.interval);
                }

                container.scrollData.interval = setInterval(() => {
                    container.scrollData.currentPosition += container.scrollData.step;

                    if (container.scrollData.currentPosition >= container.scrollData.maxScroll) {
                        container.scrollData.currentPosition = 0;
                        container.style.transition = 'none';
                        container.style.top = '0px';

                        setTimeout(() => {
                            container.style.transition = 'top 0.5s ease-in-out';
                        }, 50);
                    } else {
                        container.style.top = `-${container.scrollData.currentPosition}px`;
                    }
                }, 50);
            }

            function resetAllScrolls() {
                const scrollContainers = document.querySelectorAll('.tabela-scroll');
                scrollContainers.forEach(container => {
                    if (container.scrollData && container.scrollData.interval) {
                        clearInterval(container.scrollData.interval);
                        container.scrollData.currentPosition = 0;
                        container.style.top = '0px';
                        startScroll(container);
                    }
                });
            }

            function atualizarHoraLocal() {
                const now = new Date();
                const horaLocal = now.toLocaleTimeString('pt-BR');
                document.getElementById('hora-local').innerText = horaLocal;
            }

            function iniciarContagemRegressiva() {
                if (intervalAtualizacao) {
                    clearInterval(intervalAtualizacao);
                }

                tempoRestante = atualizacaoAuto * 60;
                atualizarContador();

                intervalAtualizacao = setInterval(() => {
                    tempoRestante--;
                    atualizarContador();

                    if (tempoRestante <= 0) {
                        recarregarPagina();
                    }
                }, 1000);
            }

            function atualizarContador() {
                const minutos = Math.floor(tempoRestante / 60);
                const segundos = tempoRestante % 60;
                const formatado = `${minutos}:${segundos.toString().padStart(2, '0')}`;
                document.getElementById('tempo-restante').innerText = formatado;
            }

            function recarregarPagina() {
                window.location.reload();
            }

            function precarregarImagensPropagandas() {
                const imagensPropagandas = document.querySelectorAll('.propaganda-imagem');
                imagensPropagandas.forEach(img => {
                    const imgPreload = new Image();
                    imgPreload.src = img.src;
                });
            }

            function setupKeyboardNavigation() {
                document.addEventListener('keydown', function(e) {
                    const carouselElement = document.getElementById('grupoCarousel');
                    if (!carouselElement) return;

                    const carousel = bootstrap.Carousel.getInstance(carouselElement);
                    if (!carousel) return;

                    if (e.key === 'ArrowLeft') {
                        carousel.prev();
                    } else if (e.key === 'ArrowRight') {
                        carousel.next();
                    } else if (e.key === 'r' || e.key === 'R') {
                        recarregarPagina();
                    }
                });
            }
        </script>
    <?php endif; ?>
</body>

</html>