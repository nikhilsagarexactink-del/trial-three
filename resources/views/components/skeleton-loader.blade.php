<style>
    * {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
body {
    height: 100vh;
    width: 100vw;
    background-color: rgb(19, 19, 64);
}

.flexbox {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* .mainbox {
    height: 400px;
    width: 400px;
    background-color: #212121;
    position: relative;
} */

.container {
    width: 260px;
    height: 250px;
    flex-direction: column;
    background-color: #fff;
    border-radius: 15px;
   gap:30px;
}
.b1,.mrow,.bcol{
       overflow: hidden;
}
.b1 {
    height: 100px;
    background-color: #d9d9d9;
    border-radius:5px;
 
}
.b2 {
    flex-direction: row;
    gap: 10px;
    padding-top: 15px;
}

.mrow {
    height: 25px;
    width: 245px;
    background-color: #d9d9d9;
    border-radius: 20px;
}
.b3{
  gap:10px;
}
.bcol {
    height: 25px;
    width: 114px;
    border-radius: 20px;
    background-color: #d9d9d9;
}

.shadow{
    height: 100px;
    width: 20px;
    background-color: #fff;
    filter: blur(10px);
    margin-left: 240px;
    animation: anim 1s  ease-out infinite ;
   
    transform: rotate(45deg);
}

@keyframes anim{
    0%{transform: translate(-200px);}
}
</style>
<div class="white-bg p-3 dash-table">
    <div class="box b1 flexbox">
        <div class="shadow"></div>
    </div>
    <div class="box b2 flexbox">
        <div class="mrow"> <div class="shadow"></div></div>
        <div class="mrow"> <div class="shadow"></div></div>
    </div>
    <div class="box b2 flexbox">
        <div class="mrow"> <div class="shadow"></div></div>
        <div class="mrow"> <div class="shadow"></div></div>
    </div><div class="box b2 flexbox">
        <div class="mrow"> <div class="shadow"></div></div>
        <div class="mrow"> <div class="shadow"></div></div>
    </div><div class="box b2 flexbox">
        <div class="mrow"> <div class="shadow"></div></div>
        <div class="mrow"> <div class="shadow"></div></div>
    </div>
</div>