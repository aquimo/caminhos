<style>
.login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.login-logo {
    margin-bottom: 30px;
}

.login-logo h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.login-logo p {
    color: #666;
    font-size: 0.9rem;
}

.login-form {
    text-align: left;
}

.login-title {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(11, 91, 54, 0.1);
}

.btn-login {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(11, 91, 54, 0.3);
}

.login-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #666;
    font-size: 0.8rem;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border-left: 4px solid;
}

.alert-error {
    background-color: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    border-color: #28a745;
    color: #155724;
}
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <h1>Bairro Ferroviário</h1>
            <p>Sistema de Gestão de Casas</p>
            <p style="color: var(--primary-color); font-size: 0.85rem; margin-top: 10px; font-style: italic;">Bem-vindo ao sistema de gestão das casas</p>
        </div>
        
        <form method="POST" class="login-form">
            <h2 class="login-title">Iniciar Sessão</h2>
            
            <?php if (SessionHelper::hasFlash('error')): ?>
                <div class="alert alert-error">
                    <?php echo SessionHelper::getFlash('error'); ?>
                </div>
            <?php endif; ?>
            
            <?php if (SessionHelper::hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?php echo SessionHelper::getFlash('success'); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="Digite o seu email" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" 
                       placeholder="Digite a sua senha" required>
            </div>
            
            <button type="submit" class="btn-login">
                Entrar
            </button>
        </form>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> Bairro Ferroviário - Sistema de Gestão</p>
            <p>Desenvolvido por: Oscar Massangaia</p>
            <p>Dados de teste: admin@caminhos.pt / password</p>
        </div>
    </div>
</div>
