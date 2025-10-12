<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Ultra Simples</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .question {
            font-size: 1.2em;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .alternative {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .alternative::before {
            content: attr(data-letter);
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            z-index: 0;
            pointer-events: none;
        }
        
        .alternative span {
            margin-left: 45px;
            flex: 1;
        }
        
        .alternative:hover {
            background: #f8f9fa;
            border-color: #007bff;
            transform: translateX(5px);
        }
        
        .alternative:active {
            transform: translateX(2px) scale(0.98);
        }
        
        .alternative.clicked {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .alternative.clicked::before {
            background: #28a745;
        }
        
        .debug {
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
        }
        
        .debug h3 {
            margin-top: 0;
        }
        
        .debug-log {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste Ultra Simples - Cliques</h1>
        
        <div class="question">
            <strong>Questão:</strong> Qual é a capital do Brasil?
        </div>
        
        <div class="alternatives">
            <div class="alternative" data-letter="A" data-alternativa-id="1" data-questao-id="1">
                <span>São Paulo</span>
            </div>
            <div class="alternative" data-letter="B" data-alternativa-id="2" data-questao-id="1">
                <span>Rio de Janeiro</span>
            </div>
            <div class="alternative" data-letter="C" data-alternativa-id="3" data-questao-id="1">
                <span>Brasília</span>
            </div>
            <div class="alternative" data-letter="D" data-alternativa-id="4" data-questao-id="1">
                <span>Belo Horizonte</span>
            </div>
        </div>
        
        <div class="debug">
            <h3>🔍 Debug Info</h3>
            <div id="debug-info">
                <div>Carregando...</div>
            </div>
            
            <h3>📝 Debug Log</h3>
            <div class="debug-log" id="debug-log">
                <div>Console de debug aparecerá aqui...</div>
            </div>
        </div>
    </div>

    <script>
        function log(message) {
            const debugLog = document.getElementById('debug-log');
            const timestamp = new Date().toLocaleTimeString();
            debugLog.innerHTML += `<div>[${timestamp}] ${message}</div>`;
            debugLog.scrollTop = debugLog.scrollHeight;
            console.log(message);
        }
        
        function updateDebugInfo() {
            const alternatives = document.querySelectorAll('.alternative');
            const debugInfo = document.getElementById('debug-info');
            
            let info = `<div><strong>Alternativas encontradas:</strong> ${alternatives.length}</div>`;
            
            alternatives.forEach((alt, index) => {
                const rect = alt.getBoundingClientRect();
                const computedStyle = window.getComputedStyle(alt);
                
                info += `<div><strong>Alt ${index + 1}:</strong></div>`;
                info += `<div>&nbsp;&nbsp;pointer-events: ${computedStyle.pointerEvents}</div>`;
                info += `<div>&nbsp;&nbsp;cursor: ${computedStyle.cursor}</div>`;
                info += `<div>&nbsp;&nbsp;z-index: ${computedStyle.zIndex}</div>`;
                info += `<div>&nbsp;&nbsp;position: ${computedStyle.position}</div>`;
                info += `<div>&nbsp;&nbsp;display: ${computedStyle.display}</div>`;
                info += `<div>&nbsp;&nbsp;---</div>`;
            });
            
            debugInfo.innerHTML = info;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            log('DOM carregado!');
            
            setTimeout(() => {
                updateDebugInfo();
                
                const alternatives = document.querySelectorAll('.alternative');
                log(`Encontradas ${alternatives.length} alternativas`);
                
                alternatives.forEach((alternative, index) => {
                    log(`Configurando alternativa ${index + 1}`);
                    
                    // Adicionar múltiplos event listeners para debug
                    alternative.addEventListener('mouseenter', function() {
                        log(`🖱️ Mouse ENTER na alternativa ${index + 1}`);
                    });
                    
                    alternative.addEventListener('mousedown', function() {
                        log(`🖱️ Mouse DOWN na alternativa ${index + 1}`);
                    });
                    
                    alternative.addEventListener('mouseup', function() {
                        log(`🖱️ Mouse UP na alternativa ${index + 1}`);
                    });
                    
                    alternative.addEventListener('click', function(e) {
                        log(`🎯 CLIQUE DETECTADO na alternativa ${index + 1}!`);
                        log(`Evento: ${e.type}, Target: ${e.target.tagName}`);
                        log(`Data attributes: questao=${this.dataset.questaoId}, alternativa=${this.dataset.alternativaId}`);
                        
                        // Remover classe clicked de todas as alternativas
                        alternatives.forEach(alt => alt.classList.remove('clicked'));
                        
                        // Adicionar classe clicked à alternativa clicada
                        this.classList.add('clicked');
                        
                        // Atualizar debug info
                        updateDebugInfo();
                    });
                });
                
                // Teste de clique programático após 3 segundos
                setTimeout(() => {
                    log('🧪 Testando clique programático...');
                    alternatives[0].click();
                }, 3000);
                
            }, 500);
        });
        
        // Teste de clique global
        document.addEventListener('click', function(e) {
            log(`🌐 Clique global detectado em: ${e.target.tagName}.${e.target.className}`);
        });
        
        // Teste de toque para dispositivos móveis
        document.addEventListener('touchstart', function(e) {
            log(`📱 Touch detectado em: ${e.target.tagName}.${e.target.className}`);
        });
    </script>
</body>
</html>


