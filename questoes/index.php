<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirecionar para página de login
    header('Location: login.php');
    exit;
}

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Questões - Resumo Acadêmico</title>
    <link rel="stylesheet" href="modern-style.css">
    <style>
    /* Padrão visual alinhado ao login */
    body {
        background-image: linear-gradient(to top, #00C6FF, #0072FF);
        min-height: 100vh;
        margin: 0;
    }
    .main-container {
        max-width: 1100px;
        margin: 40px auto;
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid transparent;
        background-image: linear-gradient(#FFFFFF, #FFFFFF), linear-gradient(to top, #00C6FF, #0072FF);
        background-origin: border-box;
        background-clip: padding-box, border-box;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        padding: 30px;
    }
    /* Removido: estilos inline de título/subtítulo do header para evitar conflito com modern-style.css */
    .user-info a { text-decoration: none; font-weight: 600; }
    .user-info a:hover { text-decoration: none; }
    /* Botão Sair aprimorado */
    .logout-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; background: linear-gradient(135deg, #00C6FF 0%, #0072FF 100%); color: #fff; border: 1px solid #bfe0ff; font-weight: 800; text-decoration: none; box-shadow: 0 8px 18px rgba(0,114,255,0.28); transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; letter-spacing: .2px; }
    .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(0,114,255,0.32); filter: brightness(1.03); }
    .logout-btn:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .logout-btn::before { content: "⎋"; font-size: 1.1rem; }
    /* Estatísticas */
    .stats-container { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
    .stat-card { background: #FFFFFF; border: 1px solid #e1e5e9; border-radius: 12px; padding: 20px; min-width: 200px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.06); }
    .stat-number { color: #0072FF; font-weight: 700; font-size: 2rem; }
    .stat-label { color: #333; }
    /* Cards */
    .cards-container { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
    .card { background: #FFFFFF; border: 1px solid #e1e5e9; border-radius: 12px; padding: 24px; width: 300px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.06); }
    .card-title { color: #333333; }
    .card-description { color: #666666; }
    .btn { display: inline-block; padding: 12px 18px; border-radius: 8px; background: linear-gradient(to top, #00C6FF, #0072FF); color: #fff; border: none; font-weight: 600; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,114,255,0.3); }
    .btn:active { transform: translateY(0); }
    /* Interações e acessibilidade */
    .btn:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .btn[aria-busy="true"] { cursor: wait; opacity: .8; }
    .card { transition: transform .2s ease, box-shadow .2s ease; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0,114,255,0.18); }
    .header { display: block; }
    .header .subtitle { font-size: 1rem; color: #555; }
    @media (max-width: 768px) {
        html, body { overflow-x: hidden; }
        .main-container { margin: 16px; padding: 18px; }
        .header .title { font-size: 1.6rem; }
        .header .subtitle { font-size: 1rem; }
        .user-info { width: 100%; display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; }
        .stats-container { flex-direction: column; gap: 12px; }
        .stat-card { width: 100%; }
        .cards-container { flex-direction: column; gap: 16px; }
        .card { width: 100%; padding: 20px; }
        .card-icon { display: block; font-size: 1.5rem; margin-bottom: 8px; }
        .card-title { margin-bottom: 6px; }
        .card-description { font-size: .95rem; }
        .btn { width: 100%; }
    }
    @media (max-width: 480px) {
        .header .title { font-size: 1.4rem; }
        .stat-number { font-size: 1.6rem; }
    }
    /* Legibilidade em cards com gradiente */
    .card[style*="linear-gradient"] .card-title { color: #fff; }
    .card[style*="linear-gradient"] .card-description { color: #f8f9fa; }
    .card[style*="linear-gradient"] .btn { box-shadow: 0 6px 16px rgba(0,114,255,0.35); }
    /* Cards clicáveis */
    .clickable-card { cursor: pointer; }
    .clickable-card:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .clickable-card:active { transform: translateY(0); }
    /* Header estiloso com paleta azul */
    .app-header { background: linear-gradient(135deg, #00C6FF 0%, #0072FF 100%); border-radius: 16px; padding: 18px; color: #fff; box-shadow: 0 12px 30px rgba(0,114,255,0.25); margin-bottom: 24px; }
    .app-header .header-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .app-header .brand { display: flex; align-items: center; gap: 12px; }
    .app-header .logo { font-size: 1.8rem; }
    .app-header .titles .title { margin: 0; color: #fff; }
    .app-header .titles .subtitle { margin: 2px 0 0; color: #eaf6ff; }
    .app-header .user-actions { display: flex; align-items: center; gap: 12px; }
    .app-header .user-name { font-weight: 600; color: #fff; }
    /* Botão Sair em vermelho */
    .logout-btn.logout-red { background: linear-gradient(180deg, #ff4b5a 0%, #dc3545 100%); color: #fff; padding: 10px 14px; border-radius: 10px; text-decoration: none; font-weight: 700; box-shadow: 0 8px 18px rgba(220,53,69,0.35); border: none; display: inline-flex; align-items: center; gap: 8px; transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; }
    .logout-btn.logout-red:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(220,53,69,0.45); filter: brightness(1.02); }
    .logout-btn.logout-red:focus { outline: 3px solid rgba(220,53,69,0.45); outline-offset: 2px; }
    @media (max-width: 768px) { .app-header .header-inner { flex-direction: column; align-items: flex-start; } .app-header .user-actions { width: 100%; justify-content: space-between; flex-wrap: wrap; } }
    /* ... */
    .card { background: #FFFFFF; border: 1px solid #e1e5e9; border-radius: 12px; padding: 24px; width: 300px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.06); }
    .card-title { color: #333333; }
    .card-description { color: #666666; }
    .btn { display: inline-block; padding: 12px 18px; border-radius: 8px; background: linear-gradient(to top, #00C6FF, #0072FF); color: #fff; border: none; font-weight: 600; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,114,255,0.3); }
    .btn:active { transform: translateY(0); }
    /* Interações e acessibilidade */
    .btn:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .btn[aria-busy="true"] { cursor: wait; opacity: .8; }
    .card { transition: transform .2s ease, box-shadow .2s ease; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(0,114,255,0.18); }
    .header { display: block; }
    .header .subtitle { font-size: 1rem; color: #555; }
    @media (max-width: 768px) {
        html, body { overflow-x: hidden; }
        .main-container { margin: 16px; padding: 18px; }
        .header .title { font-size: 1.6rem; }
        .header .subtitle { font-size: 1rem; }
        .user-info { width: 100%; display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; }
        .stats-container { flex-direction: column; gap: 12px; }
        .stat-card { width: 100%; }
        .cards-container { flex-direction: column; gap: 16px; }
        .card { width: 100%; padding: 20px; }
        .card-icon { display: block; font-size: 1.5rem; margin-bottom: 8px; }
        .card-title { margin-bottom: 6px; }
        .card-description { font-size: .95rem; }
        .btn { width: 100%; }
    }
    @media (max-width: 480px) {
        .header .title { font-size: 1.4rem; }
        .stat-number { font-size: 1.6rem; }
    }
    /* Legibilidade em cards com gradiente */
    .card[style*="linear-gradient"] .card-title { color: #fff; }
    .card[style*="linear-gradient"] .card-description { color: #f8f9fa; }
    .card[style*="linear-gradient"] .btn { box-shadow: 0 6px 16px rgba(0,114,255,0.35); }
    /* Cards clicáveis */
    .clickable-card { cursor: pointer; }
    .clickable-card:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .clickable-card:active { transform: translateY(0); }
    /* Footer estiloso */
    .app-footer { position: relative; background: #FFFFFF; border-radius: 16px; padding: 22px; box-shadow: 0 10px 24px rgba(0,0,0,0.08); margin-top: 40px; border: 1px solid #e9eef3; }
    .app-footer::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 4px; border-top-left-radius: 16px; border-top-right-radius: 16px; background: linear-gradient(90deg, #00C6FF 0%, #0072FF 100%); }
    .app-footer .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .app-footer .footer-brand { display: flex; align-items: center; gap: 12px; }
    .app-footer .footer-logo { font-size: 1.4rem; }
    .app-footer .footer-text { display: flex; flex-direction: column; }
    .app-footer .footer-text strong { color: #222; }
    .app-footer .footer-text span { color: #666; font-size: 0.95rem; }
    .app-footer .footer-nav { display: flex; align-items: center; gap: 14px; }
    .app-footer .footer-link { color: #0072FF; text-decoration: none; font-weight: 600; padding: 6px 10px; border-radius: 8px; transition: background-color .2s ease, color .2s ease; }
    .app-footer .footer-link:hover { background-color: rgba(0,114,255,0.08); }
    .app-footer .footer-link:focus { outline: 3px solid rgba(0,114,255,0.35); outline-offset: 2px; }
    .app-footer .footer-link.footer-logout { color: #dc3545; }
    .app-footer .footer-link.footer-logout:hover { background-color: rgba(220,53,69,0.10); }
    .app-footer .footer-bottom { display: flex; align-items: center; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f2f5; color: #666; font-size: 0.95rem; }
    .app-footer .footer-bottom .dot { color: #999; }
    @media (max-width: 768px) {
        .app-footer .footer-inner { flex-direction: column; align-items: flex-start; gap: 12px; }
        .app-footer .footer-nav { flex-wrap: wrap; gap: 10px; }
    }
    /* Ênfase visual no breadcrumb da página inicial */
    .index-page .header .breadcrumb .header-container {
        max-width: 1100px;
        margin: 0 auto;
        background: #FFFFFF;
        border: 2px solid #dbeafe;
        box-shadow: 0 10px 24px rgba(0,114,255,0.12);
        border-radius: 16px;
        padding: 14px 20px 16px 44px;
        position: relative;
    }
    .index-page .header .breadcrumb .header-container::before {
        content: "";
        position: absolute;
        left: 16px;
        top: 12px;
        bottom: 12px;
        width: 6px;
        border-radius: 6px;
        background: linear-gradient(180deg, #00C6FF 0%, #0072FF 100%);
    }
    .index-page .header .breadcrumb-link,
    .index-page .header .breadcrumb-current {
        font-size: 1.08rem;
        font-weight: 800;
        color: #111827;
        padding: 10px 14px;
        border-radius: 10px;
        background-color: #FFFFFF;
        border: 1px solid #CFE8FF;
        box-shadow: 0 1px 3px rgba(0,114,255,0.10);
    }
    .index-page .header .breadcrumb-current { color: #0057D9; }
    .index-page .header .breadcrumb-link:hover {
        background-color: #F0F7FF;
        color: #0057D9;
        border-color: #BBDDFF;
    }
    .index-page .header .breadcrumb-separator { color: #6B7280; font-size: 1rem; }
    /* Remover fundo em cápsula do container dos botões no header (apenas na index) */
    .index-page .header .user-info {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
        margin-bottom: 0 !important;
        animation: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    /* Bloco de perfil compacto e alinhado com os botões */
    .index-page .header .user-profile {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: #111827;
        font-weight: 700;
    }
    .index-page .header .user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #00C6FF 0%, #0072FF 100%);
        color: #fff;
        font-weight: 800;
        font-size: 0.9rem;
        box-shadow: 0 3px 8px rgba(0,114,255,0.25);
    }
    .index-page .header .user-name {
        font-size: 0.92rem;
        color: #111827;
        margin: 0;
        line-height: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .index-page .header .user-name { max-width: 120px; }
    }
    @media (max-width: 480px) {
        .index-page .header .user-name { display: none; }
        .index-page .header .user-avatar {
            width: 26px; height: 26px; font-size: 0.85rem;
        }
    }
    /* Ocultar o botão Entrar na index para destacar 'Sair' */
    .index-page .header .header-btn.primary { display: none !important; }
    /* Estilo destacado para o botão Sair no header da index (vermelho de ação) */
    .index-page .header a.header-btn[href="logout.php"] {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 8px;
        background: linear-gradient(180deg, #ff4b5a 0%, #dc3545 100%);
        color: #fff;
        border: none;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(220,53,69,0.30);
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        letter-spacing: 0;
        font-size: 0.95rem;
    }
    .index-page .header a.header-btn[href="logout.php"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(220,53,69,0.40);
        filter: brightness(1.02);
    }
    .index-page .header a.header-btn[href="logout.php"]:focus {
        outline: 3px solid rgba(220,53,69,0.45);
        outline-offset: 2px;
    }
    .index-page .header a.header-btn[href="logout.php"]::before {
        content: none;
    }
    /* Botão 'Ir para o Site' compacto */
    .index-page .header a.header-btn.site-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 8px;
        background: linear-gradient(180deg, #00C6FF 0%, #0072FF 100%);
        color: #fff;
        border: none;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,114,255,0.30);
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        font-size: 0.95rem;
    }
    .index-page .header a.header-btn.site-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(0,114,255,0.40);
        filter: brightness(1.02);
    }
    .index-page .header a.header-btn.site-link:focus {
        outline: 3px solid rgba(0,114,255,0.35);
        outline-offset: 2px;
    }
    </style>
</head>
<body class="index-page">
<?php
$breadcrumb_items = [
    ['icon' => '🏠', 'text' => 'Início', 'link' => 'index.php', 'current' => true]
];
$page_title = '';
$page_subtitle = '';
include 'header.php';
?>

        <?php
        // Buscar estatísticas do sistema
        try {
            $stmt_assuntos = $pdo->query("SELECT COUNT(*) as total FROM assuntos");
            $total_assuntos = $stmt_assuntos->fetch()['total'];
            
            $stmt_questoes = $pdo->query("SELECT COUNT(*) as total FROM questoes");
            $total_questoes = $stmt_questoes->fetch()['total'];
            
            $stmt_alternativas = $pdo->query("SELECT COUNT(*) as total FROM alternativas");
            $total_alternativas = $stmt_alternativas->fetch()['total'];
        } catch (Exception $e) {
            $total_assuntos = 0;
            $total_questoes = 0;
            $total_alternativas = 0;
        }
        ?>

        <div class="stats-container">
            <div class="stat-card slide-in-right">
                <div class="stat-number"><?php echo $total_assuntos; ?></div>
                <div class="stat-label">Assuntos</div>
            </div>
            <div class="stat-card slide-in-right">
                <div class="stat-number"><?php echo $total_questoes; ?></div>
                <div class="stat-label">Questões</div>
            </div>
            <div class="stat-card slide-in-right">
                <div class="stat-number"><?php echo $total_alternativas; ?></div>
                <div class="stat-label">Alternativas</div>
            </div>
        </div>

        <div class="cards-container">
            <!-- Card Questões -->
            <div class="card fade-in clickable-card" style="background: linear-gradient(to top, #00C6FF, #0072FF); color: white;">
                <span class="card-icon">🎯</span>
                <h3 class="card-title" style="color:#fff;">Fazer Questões</h3>
                <p class="card-description" style="color:#f8f9fa;">Teste seus conhecimentos</p>
                <a href="escolher_assunto.php" class="btn" style="box-shadow:0 6px 16px rgba(0,114,255,0.35);">Iniciar Questões</a>
            </div>

            <?php if ($_SESSION['user_type'] === 'admin'): ?>
            <!-- Card Gerenciar - Apenas para Admins -->
            <div class="card fade-in clickable-card" style="background: linear-gradient(to top, #00C6FF, #0072FF); color: white;">
                <span class="card-icon">📋</span>
                <h3 class="card-title" style="color:#fff;">Gerenciar Questões</h3>
                <p class="card-description" style="color:#f8f9fa;">Visualize, edite e organize todas as questões do sistema de forma prática.</p>
                <a href="gerenciar_questoes_sem_auth.php" class="btn" style="box-shadow:0 6px 16px rgba(0,114,255,0.35);">Gerenciar</a>
            </div>


            <?php endif; ?>
        </div>

        <?php if ($_SESSION['user_type'] === 'admin'): ?>
        <div style="margin-top: 50px;">
            <h2 style="text-align: center; margin-bottom: 30px; color: #333; font-size: 2em;">🔧 Área Administrativa</h2>
            <div class="cards-container">
                <div class="card fade-in clickable-card" style="background: linear-gradient(to top, #00C6FF, #0072FF); color: white;">
                    <span class="card-icon">👨‍💼</span>
                    <h3 class="card-title" style="color:#fff;">Dashboard Admin</h3>
                    <p class="card-description" style="color:#f8f9fa;">Acesse o painel administrativo completo do sistema.</p>
                    <a href="admin/dashboard.php" class="btn" style="background: rgba(255,255,255,0.2); border: 2px solid white;">Dashboard</a>
                </div>

                <div class="card fade-in clickable-card" style="background: linear-gradient(to top, #00C6FF, #0072FF); color: white;">
                    <span class="card-icon">📝</span>
                    <h3 class="card-title" style="color:#fff;">Adicionar Assunto</h3>
                    <p class="card-description" style="color:#f8f9fa;">Crie novos assuntos para organizar as questões.</p>
                    <a href="admin/add_assunto.php" class="btn" style="background: rgba(255,255,255,0.2); border: 2px solid white;">Novo Assunto</a>
                </div>

                <div class="card fade-in clickable-card" style="background: linear-gradient(to top, #00C6FF, #0072FF); color: white;">
                    <span class="card-icon">❓</span>
                    <h3 class="card-title" style="color:#fff;">Adicionar Questão</h3>
                    <p class="card-description" style="color:#f8f9fa;">Interface administrativa para criar questões completas.</p>
                    <a href="admin/add_questao.php" class="btn" style="background: rgba(255,255,255,0.2); border: 2px solid white;">Nova Questão</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; margin-top: 50px; padding: 40px; background: rgba(102, 126, 234, 0.1); border-radius: 16px;">
            <h2 style="color: #667eea; margin-bottom: 15px;">🔒 Área Administrativa</h2>
            <p style="color: #666; font-size: 1.1em;">Área restrita para administradores</p>
            <p style="color: #888;">Faça login como administrador para acessar essas funcionalidades</p>
        </div>
        <?php endif; ?>

<?php include 'footer.php'; ?>
    <script>
    // Garante que o botão "Sair" apareça no header da index, sem alterar a lógica de sessão
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.body.classList.contains('index-page')) return;
        const header = document.querySelector('.header');
        if (!header) return;
        const userInfo = header.querySelector('.user-info');
        if (!userInfo) return;
        let logoutBtn = header.querySelector('a.header-btn[href="logout.php"]');
        if (!logoutBtn) {
            const a = document.createElement('a');
            a.href = 'logout.php';
            a.className = 'header-btn';
            a.setAttribute('aria-label', 'Sair da sessão');
            a.innerHTML = '<i class="fas fa-sign-out-alt"></i><span>Sair</span>';
            userInfo.appendChild(a);
            logoutBtn = a;
        }
        // Garante exibição do nome do usuário logado (tenta múltiplas chaves de sessão)
        let profile = userInfo.querySelector('.user-profile');
        <?php
        $displayName = '';
        foreach ([
            'usuario_nome','usuario','nome','user_name','username','login','nome_usuario','nomeCompleto'
        ] as $k) {
            if (isset($_SESSION[$k]) && trim($_SESSION[$k]) !== '') { $displayName = $_SESSION[$k]; break; }
        }
        ?>
        const userName = "<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>";
        if (userName) {
            if (!profile) {
                const p = document.createElement('div');
                p.className = 'user-profile';
                const avatar = document.createElement('div');
                 avatar.className = 'user-avatar';
                 avatar.textContent = userName.trim().charAt(0).toUpperCase() || '?';
                 avatar.setAttribute('aria-hidden', 'true');
                 const nameEl = document.createElement('span');
                 nameEl.className = 'user-name';
                 nameEl.textContent = userName;
                 nameEl.setAttribute('title', userName);
                 p.setAttribute('aria-label', 'Usuário logado: ' + userName);
                 p.appendChild(avatar);
                 p.appendChild(nameEl);
                // posicionar perfil antes dos botões
                userInfo.insertBefore(p, userInfo.firstChild);
                profile = p;
            } else {
                // garantir estrutura compacta
                profile.classList.add('user-profile');
                let avatar = profile.querySelector('.user-avatar');
                if (!avatar) {
                    avatar = document.createElement('div');
                    avatar.className = 'user-avatar';
                    profile.insertBefore(avatar, profile.firstChild);
                }
                avatar.textContent = userName.trim().charAt(0).toUpperCase() || '?';
                let nameEl = profile.querySelector('.user-name');
                if (!nameEl) {
                    nameEl = document.createElement('span');
                    nameEl.className = 'user-name';
                    profile.appendChild(nameEl);
                }
                nameEl.textContent = userName;
            }
        }
        // Oculta o botão Entrar na index
        const loginBtn = header.querySelector('a.header-btn.primary[href="login.php"]');
        if (loginBtn) {
            loginBtn.style.display = 'none';
        }
        // Adiciona botão "Ir para o Site" ao header (abre site principal em nova aba)
        let siteBtn = header.querySelector('a.header-btn.site-link');
        if (!siteBtn) {
            const s = document.createElement('a');
            s.href = '../index.html';
            s.className = 'header-btn site-link';
            s.target = '_blank';
            s.rel = 'noopener';
            s.setAttribute('aria-label', 'Abrir site principal');
            s.innerHTML = '<i class="fas fa-globe"></i><span>Ir para o Site</span>';
            userInfo.appendChild(s);
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card.clickable-card');
        cards.forEach(card => {
            card.setAttribute('tabindex', '0');
            const go = () => {
                const link = card.querySelector('a.btn');
                if (link) window.location.href = link.getAttribute('href');
            };
            card.addEventListener('click', (e) => {
                if (e.target.closest('a')) return; // evita navegação duplicada
                go();
            });
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    go();
                }
            });
        });
    });
    </script>
</body>
</html>