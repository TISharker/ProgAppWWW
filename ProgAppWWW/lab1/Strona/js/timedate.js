function gettheDate()
{
    var Todays = new Date();
    var day = Todays.getDate();
    var month = Todays.getMonth() + 1;
    var year = Todays.getFullYear();
    
    var TheDate = (day < 10 ? "0" : "") + day + "." + (month < 10 ? "0" : "") + month + "." + year;
    document.getElementById("data").innerHTML = TheDate;
}

var timerID = null;
var timerRunning = false;

function stopclock()
{
    if(timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}

function startclock()
{
    stopclock();
    gettheDate();
    showtime();
}

function showtime()
{
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    
    var timevalue = (hours < 10 ? "0" : "") + hours;
    timevalue += (minutes < 10 ? ":0" : ":") + minutes;
    timevalue += (seconds < 10 ? ":0" : ":") + seconds;
    
    document.getElementById("zegarek").innerHTML = timevalue;
    timerID = setTimeout(showtime, 1000);
    timerRunning = true;
}
