<style>
.mystery-bag-page{position:relative;min-height:100vh;padding-bottom:60px}
#particles-canvas{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0}

/* Hero */
.mb-hero-section{position:relative;padding:60px 24px 50px;text-align:center;overflow:hidden;margin:-24px -24px 40px;border-radius:0 0 30px 30px}
.mb-hero-bg{position:absolute;inset:0;background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(168,85,247,.1),rgba(236,72,153,.08));backdrop-filter:blur(60px);z-index:0}
.mb-hero-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 20% 30%,rgba(99,102,241,.2),transparent 50%),radial-gradient(circle at 80% 70%,rgba(236,72,153,.15),transparent 50%);animation:heroGlow 8s ease-in-out infinite alternate}
.mb-hero-bg::after{content:'';position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top,var(--bg-primary),transparent);z-index:1}
@keyframes heroGlow{0%{opacity:.5;transform:scale(1)}100%{opacity:1;transform:scale(1.05)}}
.mb-hero-content{position:relative;z-index:2;max-width:700px;margin:0 auto}
.mb-hero-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 18px;background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(168,85,247,.2));border:1px solid rgba(99,102,241,.3);border-radius:50px;color:var(--accent-primary-light);font-size:.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:20px;backdrop-filter:blur(10px)}
.mb-hero-title{font-size:2.8rem;font-weight:800;color:var(--text-primary);line-height:1.2;margin-bottom:16px}
.mb-title-gradient{background:linear-gradient(135deg,#6366f1,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.mb-hero-desc{color:var(--text-secondary);font-size:1.05rem;line-height:1.7;margin-bottom:30px}
.mb-hero-stats{display:inline-flex;align-items:center;gap:24px;padding:16px 32px;background:rgba(22,22,37,.6);backdrop-filter:blur(20px);border:1px solid var(--border-color);border-radius:16px}
[data-theme="light"] .mb-hero-stats{background:rgba(255,255,255,.6)}
.mb-stat-item{display:flex;flex-direction:column;align-items:center;gap:4px}
.mb-stat-item i{font-size:1.2rem;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.mb-stat-item span{font-size:1.3rem;font-weight:800;color:var(--text-primary)}
.mb-stat-item small{font-size:.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px}
.mb-stat-divider{width:1px;height:36px;background:var(--border-color)}

/* Section */
.mb-section{position:relative;z-index:1;margin-bottom:40px}
.mb-section-header{text-align:center;margin-bottom:32px}
.mb-section-header h2{font-size:1.5rem;font-weight:700;color:var(--text-primary);margin-bottom:8px}
.mb-section-header h2 i{background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-right:8px}
.mb-section-header p{color:var(--text-secondary);font-size:.9rem}

/* Daily Check-in */
.mb-checkin-card{background:var(--gradient-card);border:1px solid var(--border-color);border-radius:20px;padding:28px;overflow:hidden}
.mb-checkin-days{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;margin-bottom:24px}
.mb-day-item{text-align:center;padding:16px 8px;border-radius:14px;transition:all .3s ease;border:1px solid var(--border-color)}
.mb-day-number{font-size:.72rem;font-weight:600;color:var(--text-secondary);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
.mb-day-icon{font-size:1.6rem;margin-bottom:6px}
.mb-day-reward{font-size:.72rem;font-weight:700}
.mb-day-checked{background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(56,189,248,.08));border-color:rgba(16,185,129,.3)}
.mb-day-checked .mb-day-icon{color:#10b981}
.mb-day-checked .mb-day-reward{color:#10b981}
.mb-day-today{background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(168,85,247,.08));border-color:rgba(99,102,241,.4);animation:todayPulse 2s ease-in-out infinite;cursor:pointer}
.mb-day-today .mb-day-icon{color:var(--accent-primary-light)}
.mb-day-today .mb-day-reward{color:var(--accent-primary)}
@keyframes todayPulse{0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,.2)}50%{box-shadow:0 0 20px 4px rgba(99,102,241,.15)}}
.mb-day-locked{opacity:.4}
.mb-day-locked .mb-day-icon{color:var(--text-muted)}
.mb-day-locked .mb-day-reward{color:var(--text-muted)}
.mb-checkin-action{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.mb-btn-checkin{padding:14px 36px;border:none;border-radius:14px;font-family:'Inter',sans-serif;font-size:.92rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .3s ease;text-decoration:none}
.mb-btn-active{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;box-shadow:0 4px 20px rgba(99,102,241,.3)}
.mb-btn-active:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(99,102,241,.4)}
.mb-btn-done{background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.3);cursor:default}
.mb-btn-disabled{background:var(--bg-input);color:var(--text-muted);border:1px solid var(--border-color)}
.mb-checkin-info{font-size:.88rem;color:var(--text-secondary)}
.mb-checkin-info strong{color:var(--accent-primary);font-size:1.1rem}

/* Bag Grid */
.mb-bag-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px}
.mb-bag-card{position:relative;background:var(--gradient-card);border:1px solid var(--border-color);border-radius:20px;overflow:hidden;cursor:pointer;transition:all .4s cubic-bezier(.4,0,.2,1)}
.mb-bag-card:hover{transform:translateY(-8px);border-color:rgba(99,102,241,.4);box-shadow:0 20px 60px rgba(0,0,0,.3),0 0 40px rgba(99,102,241,.15)}
.mb-bag-card:hover .mb-card-glow{opacity:1}
.mb-card-glow{position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(99,102,241,.08),transparent 50%);opacity:0;transition:opacity .4s;pointer-events:none}
.mb-card-badge{position:absolute;top:16px;right:16px;padding:4px 12px;border-radius:50px;font-size:.68rem;font-weight:700;letter-spacing:.5px;z-index:2;display:flex;align-items:center;gap:4px}
.mb-badge-hot{background:linear-gradient(135deg,#ef4444,#f97316);color:#fff;box-shadow:0 4px 12px rgba(239,68,68,.4);animation:badgePulse 2s ease-in-out infinite}
.mb-badge-new{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,.3)}
@keyframes badgePulse{0%,100%{box-shadow:0 4px 12px rgba(239,68,68,.4)}50%{box-shadow:0 4px 20px rgba(239,68,68,.7)}}
.mb-card-visual{padding:16px 16px 8px;display:flex;justify-content:center}
.mb-bag-img-wrap{width:100%;aspect-ratio:1/1;border-radius:14px;overflow:hidden;position:relative;border:1px solid var(--border-color)}
.mb-bag-img{width:100%;height:100%;object-fit:cover;transition:transform .5s cubic-bezier(.4,0,.2,1)}
.mb-bag-card:hover .mb-bag-img{transform:scale(1.08)}
.mb-card-info{padding:8px 24px 16px;text-align:center}
.mb-card-name{font-size:1.1rem;font-weight:700;color:var(--text-primary);margin-bottom:6px}
.mb-card-desc{font-size:.82rem;color:var(--text-secondary);line-height:1.5;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mb-card-price{display:flex;align-items:baseline;justify-content:center;gap:6px}
.mb-price-value{font-size:1.6rem;font-weight:800;background:linear-gradient(135deg,#10b981,#38bdf8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.mb-price-label{font-size:.75rem;color:var(--text-muted)}
.mb-card-actions{display:flex;gap:8px;padding:0 20px 20px}
.mb-btn-details,.mb-btn-open{flex:1;padding:10px 16px;border:none;border-radius:12px;font-family:'Inter',sans-serif;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .3s}
.mb-btn-details{background:var(--bg-input);color:var(--text-secondary);border:1px solid var(--border-color)}
.mb-btn-details:hover{color:var(--accent-primary);border-color:var(--accent-primary)}
.mb-btn-open{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;box-shadow:0 4px 15px rgba(99,102,241,.3)}
.mb-btn-open:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(99,102,241,.4)}

/* Items Panel */
.mb-items-panel{background:rgba(0,0,0,.2);border-top:1px solid var(--border-color);padding:16px 20px}
[data-theme="light"] .mb-items-panel{background:rgba(0,0,0,.03)}
.mb-items-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;font-size:.82rem;font-weight:600;color:var(--text-primary)}
.mb-items-header i{color:var(--accent-primary);margin-right:6px}
.mb-items-close{background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px;font-size:.85rem}
.mb-items-list{list-style:none;padding:0;margin:0}
.mb-items-list li{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-color-light)}
.mb-items-list li:last-child{border-bottom:none}
.mb-item-info{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--text-primary);flex:1}
.mb-item-info i{color:var(--accent-warning);font-size:.75rem;flex-shrink:0}
.mb-item-chance{display:flex;align-items:center;gap:8px;flex-shrink:0}
.mb-chance-bar{width:60px;height:4px;background:rgba(99,102,241,.1);border-radius:2px;overflow:hidden}
.mb-chance-fill{height:100%;background:linear-gradient(90deg,#ef4444,#f97316);border-radius:2px}
.mb-chance-win{background:linear-gradient(90deg,#10b981,#38bdf8)!important}
.mb-chance-text{font-size:.75rem;font-weight:700;color:var(--accent-danger);min-width:36px;text-align:right}

/* History */
.mb-history-wrapper{background:var(--gradient-card);border:1px solid var(--border-color);border-radius:16px;overflow:hidden}
.mb-history-empty{text-align:center;padding:60px 24px;color:var(--text-muted)}
.mb-history-empty i{font-size:3rem;margin-bottom:16px;display:block}
.mb-history-item{display:flex;align-items:center;gap:16px;padding:16px 24px;border-bottom:1px solid var(--border-color-light);transition:background .2s}
.mb-history-item:last-child{border-bottom:none}
.mb-history-item:hover{background:var(--bg-hover)}
.mb-history-avatar{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(168,85,247,.15));display:flex;align-items:center;justify-content:center;flex-shrink:0}
.mb-history-avatar i{font-size:1.3rem;color:var(--accent-primary)}
.mb-history-info{flex:1;min-width:0}
.mb-history-user{font-weight:600;font-size:.88rem;color:var(--text-primary);margin-bottom:4px}
.mb-history-badge{display:inline-block;padding:2px 10px;background:rgba(99,102,241,.12);color:var(--accent-primary);border-radius:50px;font-size:.7rem;font-weight:600}
.mb-history-reward{text-align:right;flex-shrink:0}
.mb-history-item-name{font-weight:600;font-size:.85rem;color:var(--accent-success);margin-bottom:2px}
.mb-history-item-content{font-size:.75rem;color:var(--text-muted)}
.mb-history-time{font-size:.75rem;color:var(--text-muted);white-space:nowrap;flex-shrink:0}

/* Modal */
.mb-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;z-index:9999;padding:20px}
.mb-modal-overlay.active{display:flex}
.mb-modal{background:var(--bg-card);border:1px solid var(--border-color);border-radius:20px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;position:relative;animation:modalSlideIn .3s cubic-bezier(.4,0,.2,1);box-shadow:0 24px 80px rgba(0,0,0,.4)}
@keyframes modalSlideIn{from{opacity:0;transform:translateY(30px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
.mb-modal-close{position:absolute;top:16px;right:16px;background:var(--bg-input);border:1px solid var(--border-color);color:var(--text-muted);width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;font-size:.85rem;transition:all .2s}
.mb-modal-close:hover{color:var(--accent-danger);border-color:var(--accent-danger)}
.mb-modal-header{text-align:center;padding:32px 24px 16px}
.mb-modal-icon{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(168,85,247,.15));display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.mb-modal-icon i{font-size:1.5rem;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.mb-modal-header h3{font-size:1.2rem;font-weight:700;color:var(--text-primary)}
.mb-modal-body{padding:0 24px 24px}
.mb-modal-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0}
.mb-modal-row span{color:var(--text-secondary);font-size:.88rem}
.mb-modal-row strong{font-size:.88rem;color:var(--text-primary)}
.mb-modal-total span{font-size:1rem;font-weight:600;color:var(--text-primary)}
.mb-modal-total strong{font-size:1.2rem}
.mb-text-accent{color:var(--accent-primary)!important}.mb-text-info{color:var(--accent-info)!important}.mb-text-success{color:var(--accent-success)!important}.mb-text-warning{color:var(--accent-warning)!important}.mb-text-danger{color:var(--accent-danger)!important}
.mb-modal-divider{height:1px;background:var(--border-color);margin:8px 0}
.mb-modal-footer{display:flex;gap:12px;padding:0 24px 24px}
.mb-modal-btn{flex:1;padding:14px 20px;border:none;border-radius:14px;font-family:'Inter',sans-serif;font-size:.92rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .3s}
.mb-modal-btn-cancel{background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-color)}
.mb-modal-btn-confirm{background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;box-shadow:0 4px 20px rgba(99,102,241,.3)}
.mb-modal-btn-confirm:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(99,102,241,.4)}

/* Toggle Switch */
.mb-toggle-switch{position:relative;display:inline-block;width:48px;height:26px}
.mb-toggle-switch input{opacity:0;width:0;height:0}
.mb-toggle-slider{position:absolute;cursor:pointer;inset:0;background:var(--bg-input);border:1px solid var(--border-color);transition:.3s;border-radius:26px}
.mb-toggle-slider::before{content:'';position:absolute;height:20px;width:20px;left:2px;bottom:2px;background:#fff;transition:.3s;border-radius:50%}
.mb-toggle-switch input:checked+.mb-toggle-slider{background:linear-gradient(135deg,#6366f1,#a855f7);border-color:transparent}
.mb-toggle-switch input:checked+.mb-toggle-slider::before{transform:translateX(22px)}

/* Result */
.mb-modal-result .mb-modal-body{padding:48px 32px}
.mb-loading-spinner{width:80px;height:80px;position:relative;margin:0 auto 24px;display:flex;align-items:center;justify-content:center}
.mb-spinner-ring{position:absolute;inset:0;border:3px solid transparent;border-top-color:var(--accent-primary);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.mb-loading-spinner>i{font-size:1.8rem;color:var(--accent-warning);animation:pulse 1s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15);opacity:.7}}
#loading-animation h4{color:var(--text-primary);font-size:1.1rem;margin-bottom:8px}
.mb-result-icon{width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;animation:resultBounce .8s cubic-bezier(.4,0,.2,1)}
@keyframes resultBounce{0%{transform:scale(0);opacity:0}50%{transform:scale(1.2)}100%{transform:scale(1);opacity:1}}
.mb-result-success-icon{background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(56,189,248,.15))}
.mb-result-success-icon i{font-size:2rem;background:linear-gradient(135deg,#10b981,#38bdf8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.mb-result-error-icon{background:linear-gradient(135deg,rgba(239,68,68,.15),rgba(249,115,22,.15))}
.mb-result-error-icon i{font-size:2rem;color:var(--accent-danger)}
.mb-result-title{font-size:1.4rem;font-weight:700;color:var(--accent-success);margin-bottom:8px}
.mb-result-desc{color:var(--text-secondary);font-size:.95rem;margin-bottom:20px}
.mb-result-reward{background:rgba(0,0,0,.2);border:1px solid var(--border-color);border-radius:14px;padding:16px 20px;margin-bottom:24px}
[data-theme="light"] .mb-result-reward{background:rgba(0,0,0,.03)}
.mb-result-reward small{display:block;color:var(--text-muted);font-size:.75rem;margin-bottom:6px}
.mb-result-reward strong{font-size:1.1rem;color:var(--accent-warning)}
.text-center{text-align:center}

/* Responsive */
@media(max-width:768px){
    .mb-hero-title{font-size:2rem}
    .mb-hero-stats{flex-direction:column;gap:12px;padding:16px 24px}
    .mb-stat-divider{width:60px;height:1px}
    .mb-bag-grid{grid-template-columns:1fr;gap:16px}
    .mb-checkin-days{grid-template-columns:repeat(4,1fr)}
    .mb-checkin-action{flex-direction:column;align-items:stretch;text-align:center}
    .mb-history-item{flex-wrap:wrap;gap:10px}
    .mb-history-reward,.mb-history-time{flex:1 1 100%;padding-left:58px;text-align:left}
    .mb-modal{margin:12px;max-height:95vh}
}
@media(min-width:769px) and (max-width:1024px){.mb-bag-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1025px){.mb-bag-grid{grid-template-columns:repeat(3,1fr)}}
</style>
