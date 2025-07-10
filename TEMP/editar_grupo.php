   <div class="container py-5 wrapper">
       <main>
           <div class="row justify-content-center">
               <div class="col-md-8">
                   <!-- Mensagens de feedback -->
                   <?php if (!empty($mensagem)): ?>
                       <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                           <?= htmlspecialchars($mensagem) ?>
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                       </div>
                   <?php endif; ?>
                   <div class="card shadow">
                       <div class="card-header bg-primary text-white">
                           <h2 class="h4 mb-0 text-center">Editar Grupo</h2>
                       </div>
                       <div class="card-body">
                           <form method="post" id="formEditar" class="needs-validation" novalidate>
                               <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                               <input type="hidden" name="id" value="<?= $id ?>">
                               <div class="mb-3">
                                   <label for="nome" class="form-label">Nome do Grupo</label>
                                   <input type="text" class="form-control" id="nome" name="nome"
                                       value="<?= htmlspecialchars($grupo['nome']) ?>" required>
                                   <div class="invalid-feedback">
                                       O nome do grupo não pode estar vazio.
                                   </div>
                               </div>
                               <div class="d-flex justify-content-between mt-4">
                                   <a href="cadastro_grupos.php" class="btn btn-secondary">
                                       <i class="bi bi-arrow-left"></i> Voltar
                                   </a>
                                   <button type="submit" class="btn btn-success">
                                       <i class="bi bi-check-lg"></i> Salvar Alterações
                                   </button>
                               </div>
                           </form>
                       </div>
                   </div>
               </div>
           </div>
       </main>
   </div>


   <script>
       // Validação do formulário
       (function() {
           'use strict';

           const forms = document.querySelectorAll('.needs-validation');

           Array.from(forms).forEach(form => {
               form.addEventListener('submit', event => {
                   if (!form.checkValidity()) {
                       event.preventDefault();
                       event.stopPropagation();
                   }

                   form.classList.add('was-validated');
               }, false);
           });
       })();

       // Fechar alertas automaticamente após 5 segundos
       document.addEventListener('DOMContentLoaded', function() {
           const alertList = document.querySelectorAll('.alert');
           alertList.forEach(function(alert) {
               setTimeout(function() {
                   const bsAlert = new bootstrap.Alert(alert);
                   bsAlert.close();
               }, 5000);
           });
       });
   </script>