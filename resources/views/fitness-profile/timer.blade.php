@extends('layouts.app')
@section('head')
<title>Add Workout | Add</title>
@endsection

@section('content')
@include('layouts.sidebar')

<div class="content-wrapper">
  <div class="stopwatch ">
    <div class="clockwrapper">
      <h1>READY</h1>
      <div class="outerdot paused"></div>
      <div class="clock">
        00 : 00
      </div>
    </div>

    <div class="controls">
      <div class="start btn"><i class="fas fa-play"></i> Start</div>
      <div class="stop btn"><i class="fas fa-redo-alt"></i> Reset</div>
    </div>
  </div>
</div>
<script>
  var start = document.querySelector(".start"),
    stop = document.querySelector(".stop"),
    clock = document.querySelector(".clock"),
    seconds = document.querySelector(".outerdot"),
    timerState = "stopped", //Clock is either stopped, paused, or running
    startTime, elapsed, timer;
  //timer states

  start.addEventListener("click", function() {
    if (timerState == "stopped") {
      startTime = Date.now();
      console.log();
      timer = requestAnimationFrame(updateTime);
      timerState = "running";
      seconds.classList.remove("paused");
      seconds.classList.add("running");
      start.innerHTML = '<i class="fas fa-stop"></i> Stop';
    } else if (timerState == "running") {
      cancelAnimationFrame(timer);
      timerState = "paused";
      seconds.classList.add("paused");
      stop.classList.remove("disabled");
      start.innerHTML = '<i class="fas fa-play"></i> Resume';
    } else if (timerState == "paused") {
      startTime = Date.now() - elapsed;
      timer = requestAnimationFrame(updateTime);
      timerState = "running";
      seconds.classList.remove("paused");
      seconds.classList.add("running");
      stop.classList.add("disabled");
      start.innerHTML = '<i class="fas fa-stop"></i> Stop';
    }
  })

  stop.addEventListener("click", function() {
    if (!this.classList.contains("disabled")) {
      timerState = "stopped";
      seconds.classList.remove("paused", "running");
      this.classList.add("disabled");
      start.innerHTML = '<i class="fas fa-play"></i> Start';
      clock.innerHTML = "00 : 00"
    }
  });

  function updateTime() {
    timer = requestAnimationFrame(updateTime);
    elapsed = new Date(Date.now() - startTime);

    // Total elapsed time in milliseconds
    var totalMilliseconds = elapsed.getTime();

    // Calculate minutes and seconds
    var mins = Math.floor(totalMilliseconds / (1000 * 60));
    var secs = Math.floor((totalMilliseconds % (1000 * 60)) / 1000);

    // Add leading zeros if necessary
    mins = mins < 10 ? "0" + mins : mins;
    secs = secs < 10 ? "0" + secs : secs;

    // Update clock
    clock.innerHTML = mins + ":" + secs;
  };
</script>
<!-- <style>
    body {
    width: 100vw;
    height: 100vh;
    /* background: radial-gradient(ellipse at center, #320240 20%, #000 100%) no-repeat; */
    font-family: 'PT Sans', sans-serif;
    /* display: flex;
    justify-content: center;
    align-items: center; */
  }
</style> -->