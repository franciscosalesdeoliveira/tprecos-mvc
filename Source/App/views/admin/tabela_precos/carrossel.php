<?php if (!empty($propagandas)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div id="propagandaCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="<?= $config['tempo_propagandas'] ?>">
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    <?php foreach ($propagandas as $index => $propaganda): ?>
                        <button type="button"
                            data-bs-target="#propagandaCarousel"
                            data-bs-slide-to="<?= $index ?>"
                            <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?>
                            aria-label="Propaganda <?= $index + 1 ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Slides das propagandas -->
                <div class="carousel-inner">
                    <?php foreach ($propagandas as $index => $propaganda): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body text-center py-4">
                                    <?php if (!empty($propaganda['imagem'])): ?>
                                        <img src="<?= htmlspecialchars($propaganda['imagem']) ?>"
                                            alt="<?= htmlspecialchars($propaganda['titulo'] ?? 'Propaganda') ?>"
                                            class="img-fluid mb-3"
                                            style="max-height: 200px; object-fit: contain;">
                                    <?php endif; ?>

                                    <?php if (!empty($propaganda['titulo'])): ?>
                                        <h3 class="card-title"><?= htmlspecialchars($propaganda['titulo']) ?></h3>
                                    <?php endif; ?>

                                    <?php if (!empty($propaganda['descricao'])): ?>
                                        <p class="card-text"><?= htmlspecialchars($propaganda['descricao']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($propaganda['preco_destaque'])): ?>
                                        <div class="h2 text-warning">
                                            R$ <?= number_format($propaganda['preco_destaque'], 2, ',', '.') ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($propaganda['validade'])): ?>
                                        <small class="text-light">
                                            Válido até: <?= date('d/m/Y', strtotime($propaganda['validade'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controles de navegação -->
                <button class="carousel-control-prev" type="button" data-bs-target="#propagandaCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propagandaCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configurar carrossel de propagandas
        document.addEventListener('DOMContentLoaded', function() {
            const propagandaCarousel = document.getElementById('propagandaCarousel');
            if (propagandaCarousel) {
                // Inicializar carrossel do Bootstrap
                const carousel = new bootstrap.Carousel(propagandaCarousel, {
                    interval: <?= $config['tempo_propagandas'] ?>,
                    wrap: true,
                    keyboard: true
                });

                // Pausar carrossel ao passar o mouse
                propagandaCarousel.addEventListener('mouseenter', function() {
                    carousel.pause();
                });

                // Retomar carrossel ao sair o mouse
                propagandaCarousel.addEventListener('mouseleave', function() {
                    carousel.cycle();
                });
            }
        });
    </script>
<?php endif; ?>