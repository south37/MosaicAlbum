window.onload = ->
    canvas = document.getElementById("cvs1")
    ctx = canvas.getContext('2d')
    ctx.fillStyle = "#000000"
    ctx.beginPath()
    ctx.fillRect 50,50,100,100
    console.log "here"
    console.log "clicked aa"
 
