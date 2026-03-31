<script>
document.addEventListener('DOMContentLoaded', function(){
    var csrfToken='<?= $_SESSION['csrf_token'] ?? '' ?>';

    // Particles
    var canvas=document.getElementById('particles-canvas');
    if(canvas){
        var ctx=canvas.getContext('2d'),particles=[];
        function resizeCanvas(){canvas.width=window.innerWidth;canvas.height=window.innerHeight}
        resizeCanvas();window.addEventListener('resize',resizeCanvas);
        var colors=['#fbbf24','#a78bfa','#38bdf8','#f472b6','#6366f1'];
        for(var i=0;i<25;i++){
            particles.push({x:Math.random()*canvas.width,y:Math.random()*canvas.height,
                size:Math.random()*4+1,sx:(Math.random()-.5)*.5,sy:Math.random()*-.8-.2,
                op:Math.random()*.5+.1,rot:Math.random()*Math.PI*2,rs:(Math.random()-.5)*.02,
                color:colors[Math.floor(Math.random()*colors.length)]});
        }
        function animate(){
            ctx.clearRect(0,0,canvas.width,canvas.height);
            particles.forEach(function(p){
                p.x+=p.sx;p.y+=p.sy;p.rot+=p.rs;
                if(p.y<-20){p.y=canvas.height+20;p.x=Math.random()*canvas.width}
                ctx.save();ctx.translate(p.x,p.y);ctx.rotate(p.rot);
                ctx.globalAlpha=Math.max(0,Math.min(1,p.op+Math.sin(Date.now()*.001+p.x)*.1));
                ctx.fillStyle=p.color;ctx.shadowColor=p.color;ctx.shadowBlur=6;
                ctx.beginPath();ctx.moveTo(0,-p.size);ctx.lineTo(p.size*.6,0);
                ctx.lineTo(0,p.size);ctx.lineTo(-p.size*.6,0);ctx.closePath();ctx.fill();
                ctx.restore();
            });
            requestAnimationFrame(animate);
        }
        animate();
    }

    // Check-in
    var btnCheckin=document.getElementById('btn-checkin');
    if(btnCheckin){
        btnCheckin.addEventListener('click',function(){
            btnCheckin.disabled=true;btnCheckin.innerHTML='<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            fetch('<?= url('/mystery-bag/checkin') ?>',{
                method:'POST',
                headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded'},
                body:'csrf_token='+csrfToken
            }).then(function(r){return r.json()}).then(function(data){
                if(data.csrf_token) csrfToken=data.csrf_token;
                if(data.status==='success'){
                    btnCheckin.innerHTML='<i class="fas fa-check"></i> '+data.message;
                    btnCheckin.className='mb-btn-checkin mb-btn-done';
                    var sd=document.getElementById('free-spins-display');if(sd)sd.textContent=data.total_spins;
                    var sc=document.getElementById('free-spins-count');if(sc)sc.textContent=data.total_spins;
                    var mf=document.getElementById('modal-free-spins');if(mf)mf.textContent=data.total_spins;
                    var days=document.querySelectorAll('.mb-day-item');
                    if(days[data.day-1]){
                        days[data.day-1].className='mb-day-item mb-day-checked';
                        days[data.day-1].querySelector('.mb-day-icon').innerHTML='<i class="fas fa-check-circle"></i>';
                    }
                    if(data.day<7&&days[data.day]){days[data.day].className='mb-day-item mb-day-locked'}
                }else{
                    btnCheckin.innerHTML='<i class="fas fa-exclamation-triangle"></i> '+data.message;
                    btnCheckin.disabled=false;
                }
            }).catch(function(){btnCheckin.innerHTML='Lỗi, thử lại!';btnCheckin.disabled=false});
        });
    }

    // Purchase Modal
    var currentBagId=null,currentBagPrice=0;
    window.showPurchaseModal=function(id,name,price){
        currentBagId=id;currentBagPrice=price;
        document.getElementById('modal-bag-name').textContent=name;
        document.getElementById('modal-price').textContent=new Intl.NumberFormat('vi-VN').format(price)+'đ';
        // Auto-enable free spin if available
        var freeCount=parseInt(document.getElementById('modal-free-spins').textContent)||0;
        document.getElementById('modal-use-free').checked=freeCount>0;
        updatePaymentInfo();
        document.getElementById('purchaseModal').classList.add('active');
    };
    window.closePurchaseModal=function(){document.getElementById('purchaseModal').classList.remove('active')};
    window.updatePaymentInfo=function(){
        var useFree=document.getElementById('modal-use-free').checked;
        document.getElementById('modal-total').textContent=useFree?'Miễn phí (1 lượt)':new Intl.NumberFormat('vi-VN').format(currentBagPrice)+'đ';
        document.getElementById('modal-total').className=useFree?'mb-text-success':'mb-text-warning';
    };

    document.getElementById('btn-confirm-purchase').addEventListener('click',function(){
        var useFree=document.getElementById('modal-use-free').checked;
        closePurchaseModal();openBag(currentBagId,useFree);
    });

    function openBag(bagId,useFree){
        var loading=document.getElementById('loading-animation'),
            resultBox=document.getElementById('result-content'),
            errorBox=document.getElementById('error-content');
        loading.style.display='block';resultBox.style.display='none';errorBox.style.display='none';
        document.getElementById('resultModal').classList.add('active');
        setTimeout(function(){
            var body='csrf_token='+csrfToken;
            if(useFree)body+='&use_free_spin=1';
            fetch('<?= url('/mystery-bag/open/') ?>'+bagId,{
                method:'POST',
                headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded'},
                body:body
            }).then(function(r){return r.json()}).then(function(data){
                if(data.csrf_token) csrfToken=data.csrf_token;
                loading.style.display='none';
                if(data.status==='error'){
                    document.getElementById('error-desc').innerText=data.message;
                    errorBox.style.display='block';
                }else{
                    var icon=document.querySelector('.mb-result-success-icon i');
                    var title=document.getElementById('result-title');
                    if(data.is_lucky){
                        title.textContent='🎉 Chúc mừng!';title.style.color='var(--accent-success)';
                        if(icon){icon.className='fas fa-coins';icon.style.cssText='font-size:2rem;color:#10b981'}
                    }else{
                        title.textContent='😢 Tiếc quá!';title.style.color='var(--accent-warning)';
                        if(icon){icon.className='fas fa-sad-tear';icon.style.cssText='font-size:2rem;color:#f59e0b'}
                    }
                    document.getElementById('result-desc').innerText=data.item_name;
                    document.getElementById('result-detail').innerText=data.item_content;
                    document.querySelectorAll('.user-balance').forEach(function(el){el.innerHTML='<i class="fas fa-wallet"></i> '+data.balance});
                    if(data.free_spins!==undefined){
                        var sd=document.getElementById('free-spins-display');if(sd)sd.textContent=data.free_spins;
                        var sc=document.getElementById('free-spins-count');if(sc)sc.textContent=data.free_spins;
                        var mf=document.getElementById('modal-free-spins');if(mf)mf.textContent=data.free_spins;
                    }
                    resultBox.style.display='block';
                }
            }).catch(function(){
                loading.style.display='none';
                document.getElementById('error-desc').innerText='Lỗi mạng, thử lại!';
                errorBox.style.display='block';
            });
        },1200);
    }

    window.closeResultModal=function(){document.getElementById('resultModal').classList.remove('active')};
    window.toggleItems=function(id){var p=document.getElementById('items-panel-'+id);if(p)p.style.display=p.style.display==='none'?'block':'none'};
    document.querySelectorAll('.mb-modal-overlay').forEach(function(o){
        o.addEventListener('click',function(e){if(e.target===this)this.classList.remove('active')});
    });
    document.querySelectorAll('.mb-bag-card').forEach(function(c){
        c.addEventListener('click',function(){var b=this.querySelector('.mb-btn-open');if(b)b.click()});
    });
});
</script>
