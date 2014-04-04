$(document).ready(function(){
    var canvas = $('#canvas')[0];
    var ctx = canvas.getContext('2d');
    ctx.canvas.width = 640;
    ctx.canvas.height = 400;
    
    var cWidth = $('#canvas').width();
    var cHeight = $('#canvas').height();
    
    var game_loop;
    
    //Background
    var margeTop = (cHeight/100)*10;
    var BgImg = new Image();
    BgImg.src = "img/bg.png";
    
    //player
    var player;
    var startJump;
    var playerJumpAlt = 30;
    var playersize = 20;
    var playerJumpVelocity = 10;
    var playerImg = new Image();
    playerImg.src = "img/player.png";
    
    // pipe
    var pipeTimer;
    var pipeVelocidad = 5;
    var pipeSize = 40;
    var pipeNumber = 3;
    var pipediff = 70;
    var pipe = new Array(pipeNumber * 2);
    var pipeImgTop = new Image();
    pipeImgTop.src = "img/pipeTop.png";
    var pipeImgBottom = new Image();
    pipeImgBottom.src = "img/pipeBottom.png";
    
    
    $(document).keydown(function(event){
        var key = event.which;
        if(key == 32 || key == 38){
            event.preventDefault();
            startJump = true;
        }
        if(key == 13){
            event.preventDefault();
            clearInterval(game_loop);
            init();
        }
    });
    
    function init(){
        ctx.save();
        ctx.clearRect(0,0, 640,400);
        ctx.restore();
        
        // game
        clearInterval(game_loop);
        game_loop = setInterval(main,30);
        
        //player
        player = {
            oldPosX: (cWidth/100)*10,
            oldPosY: (cHeight/2)-10,
            posX: (cWidth/100)*10,
            posY:(cHeight/2)-10,
            width: playersize,
            height: playersize,
            jumping: false,
            startJumpPos: 0,
            jumpVelocity: 0
        };
        
        startJump = false;
        pipeTimer = Math.floor( cWidth / (pipe.length/2) ) / pipeVelocidad;
        
        //pipe
        for(var i = 0; i < pipe.length; i++){
            pipe[i]={draw: false,
                onScreen: false,
                count: false,
                posX: cWidth,
                posY:0,
                width: pipeSize,
                height: 0};
        }
        
        pipeNumber = 0;
    }
    
    function main(){
        ctx.clearRect(0,0, 640,400);
        drawBackground();
        
        // pipe
        drawPipe();
        movePipe();
        
        // player
        drawPlayer();
        if(player.jumping || startJump){
            playerJump();
        }else{
            playerGravity();
        }
        
        // collision
        borderCollision();
    }
    
    function gameover(){
        ctx.save();
        /*ctx.clearRect(0,0, 640,400);*/
        /*drawBackground();*/
        //Background
        var GameOverImg = new Image();
        GameOverImg.src = "img/gameOver.png";
   
        ctx.drawImage(GameOverImg,220,150,200,100);
        ctx.restore();
        
        clearInterval(game_loop);
    }
    
    
    
    function drawBackground(){
        ctx.save();
        ctx.drawImage(BgImg,0,0,cWidth,cHeight);
        ctx.restore();
    }
    
    function drawPlayer(){
        ctx.save();
        /*ctx.clearRect(player.oldPosX,player.oldPosY, playersize,playersize);*/
        ctx.drawImage(playerImg,player.posX,player.posY,playersize,playersize);
        player.oldPosX = player.posX;
        player.oldPosY = player.posY;
        ctx.restore();
    }
    
    function playerGravity(){
        player.posY += 5;
    }
    
    function borderCollision(){
        if(player.posY < margeTop ){
            CatchCollision();
        }
            
        if( player.posY > cHeight - margeTop ){
            CatchCollision();
        }
        
        for(var i = 0; i < pipe.length; i++){
            if( player.posX + player.width > pipe[i].posX &&
                player.posX < pipe[i].posX + pipe[i].width &&
                player.posY < pipe[i].posY + pipe[i].height &&
                player.posY + player.height > pipe[i].posY
            ){
                CatchCollision();
            }
        }
    }
    
    function CatchCollision(){
        clearInterval(game_loop);
        game_loop = setInterval(gameover,1);
    }
    
    function playerJump(){
        if(startJump){
            player.jumping = true;
            player.startJumpPos = player.posY;
            player.jumpVelocity = playerJumpVelocity;
            startJump = false;
        }
        if(player.posY > (player.startJumpPos - playerJumpAlt)){
            if(player.jumpVelocity > 3){
                player.jumpVelocity -= 2;
            }
            player.posY -= player.jumpVelocity;
        }else{
            player.jumping = false;
        }
    }
    
    
    function generatePipe(pipenum){
        var ind = pipenum * 2;
        pipe[ind].posX = cWidth;
        pipe[ind].posY =  margeTop;
        pipe[ind].height = Math.floor((Math.random()*(cHeight - (2 * margeTop) - pipediff)));
        pipe[ind].oldposX = pipe[ind].posX;
        pipe[ind].oldposY = pipe[ind].posY;
        pipe[ind].draw = true;
        
        pipe[ind+1].posX = cWidth;
        pipe[ind+1].posY = pipe[ind].height + pipe[ind].posY + pipediff ;
        pipe[ind+1].height = (cHeight - margeTop) - (pipe[ind].height + pipe[ind].posY + pipediff);
        pipe[ind+1].oldposX = pipe[ind+1].posX;
        pipe[ind+1].oldposY = pipe[ind+1].posY;
        pipe[ind+1].draw = true;
    }
    
    
    function drawPipe(){
        pipeTimer++;
        if( pipeTimer > Math.floor( cWidth / (pipe.length/2) + pipeSize) / pipeVelocidad ){
            if(pipeNumber >= pipe.length /2){
                pipeNumber = 0;
            }else{
                pipeNumber += 1;
            }
            generatePipe(pipeNumber);
            pipeTimer = 0;
        }
        ctx.save();
        for(var i = 0; i < pipe.length; i++){
            if(pipe[i].draw){
                /*ctx.clearRect(pipe[i].oldPosX,pipe[i].oldPosY, pipe[i].width, pipe[i].height);*/
                ctx.fillStyle = 'black';
                
                if( i%2 == 0){
                    ctx.fillRect(pipe[i].posX,pipe[i].posY,pipe[i].width,pipe[i].height-pipeSize);
                    ctx.drawImage(pipeImgTop,pipe[i].posX,pipe[i].posY+pipe[i].height-pipeSize,pipeSize,pipeSize);
                }else{
                    ctx.fillRect(pipe[i].posX,pipe[i].posY+pipeSize,pipe[i].width,pipe[i].height);
                    ctx.drawImage(pipeImgBottom,pipe[i].posX,pipe[i].posY,pipeSize,pipeSize);
                }
                pipe[i].oldPosX = pipe[i].posX;
                pipe[i].oldPosY = pipe[i].posY;
            }
        }
        ctx.restore();
    }
    
    function movePipe(){
        for(var i = 0; i < pipe.length; i++){
            if(pipe[i].draw){
                pipe[i].posX -= 5;
            }
        }
    }
    
    init();
});