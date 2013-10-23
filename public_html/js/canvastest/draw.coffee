$ ->#jquery使えるようにする
  $("#cvs1").ready =>
    stage = new createjs.Stage("cvs1")#canvas要素のid文字列．#いらない.
    circle = new createjs.Shape()
    circle.graphics.beginFill("red").drawCircle(0,0,50)
    circle.x = 100
    circle.y = 100
    stage.addChild circle
    stage.addChild makeSquare(300,200)
    
    #preloader
    path = "/img/miku.jpg"

    queue = new createjs.LoadQueue(true)
    queue.addEventListener "complete", ->
      console.log("complete!")
      btm = new createjs.Bitmap(queue.getResult("image"))
      btm.x = 300
      
      size = 200
      btm.scaleX = size / btm.image.width
      btm.scaleY = size / btm.image.height
      console.log btm.scaleX 
      stage.addChild btm
      stage.update()
      createjs.Tween.get(btm)
                    .to((x:0,y:300),3000,createjs.Ease.bounceOut)
                    .call ->
                      console.log "fin tween"
     # createjs.Ticker.addListener(window)
    queue.loadFile
      id:"image"
      src:path
    queue.load()
    
    stage.update()

    createjs.Ticker.addEventListener("tick",stage)


  makeSquare = (x=200,y=200) ->
    square = new createjs.Shape()
    square.graphics.beginFill("blue").drawRect(0,0,100,100)
    square.x = x
    square.y = y 
    square.addEventListener "click", (e)->
      # ajaxロード
      console.log("image clicked")
      $.getJSON "/common/mosaic_viewer_test/ajax", (data)->
        console.log data
    return square

  loadBitmapImg = (path,x=300,y=200) ->
    img = new createjs.Bitmap(path)
    img.x = x
    img.y = y
    console.log("makeimg")
    return img

 
