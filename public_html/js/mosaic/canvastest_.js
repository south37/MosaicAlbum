window.onload = drawCircle;
function drawCircle(){
    var canvas = document.getElementById("cvs1");
    var ctx = canvas.getContext('2d');
    ctx.fillStyle = "#000000";
    ctx.beginPath();
    ctx.arc(100, 100, 50, 0,Math.PI * 2, true);
    ctx.fill();
}
