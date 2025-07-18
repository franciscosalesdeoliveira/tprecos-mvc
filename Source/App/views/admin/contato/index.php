<?php $this->layout("_theme", ["title" => $title]); ?>

<?php $this->start("conteudo"); ?>
<div class="page-wrapper">
    <div class="main-content">
        <div class="contact-container">
            <div class="contact-header">
                <h1 class="contact-title">Fale Conosco</h1>
                <p class="contact-subtitle">Estamos à disposição para ajudar você</p>
                <div class="contact-wave">
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                    </svg>
                </div>
            </div>

            <div class="contact-content">


                <div class="contact-options">
                    <div class="contact-form-container">
                        <h2 class="section-title">Envie sua mensagem</h2>

                        <form method="post" action="<?= url("admin/contato") ?>">
                            <div class="form-group">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="assunto" class="form-label">Assunto</label>
                                <input type="text" id="assunto" name="assunto" class="form-control" required value="<?php echo isset($_POST['assunto']) ? htmlspecialchars($_POST['assunto']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="mensagem" class="form-label">Mensagem</label>
                                <textarea id="mensagem" name="mensagem" class="form-control" required><?php echo isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Enviar Mensagem</button>
                        </form>
                    </div>

                    <div class="whatsapp-container">

                        <h2 class="section-title">Atendimento via WhatsApp</h2>
                        <p>Precisa de ajuda imediata? Entre em contato conosco pelo WhatsApp para um atendimento rápido e personalizado.</p>

                        <a href="https://wa.me/5515981813900" class="whatsapp-button" target="_blank">
                            <i class="fab fa-whatsapp"></i> Iniciar Conversa
                        </a>

                        <div class="whatsapp-number">
                            <i class="fas fa-phone"></i> (15) 98181-3900
                        </div>

                        <div class="whatsapp-hours">
                            <h4><i class="far fa-clock"></i> Horários de Atendimento</h4>
                            <p>Segunda à Sexta: 08:00 - 18:00</p>
                            <p>Sábado: 08:00 - 12:00</p>
                            <p>Domingo e Feriados: Fechado</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $this->stop(); ?>