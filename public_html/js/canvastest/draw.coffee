$ ->#jquery使えるようにする
  $("#cvs1").ready =>
    stage = new createjs.Stage("cvs1")
    circle = new createjs.Shape()
    circle.graphics.beginFill("red").drawCircle(0,0,50)
    circle.x = 100
    circle.y = 100
    stage.addChild circle
    stage.addChild makeSquare()
    stage.update()

  makeSquare = (x=200,y=200) ->
    square = new createjs.Shape()
    square.graphics.beginFill("blue").drawRect(0,0,100,100)
    square.x = x
    square.y = y 
    return square
