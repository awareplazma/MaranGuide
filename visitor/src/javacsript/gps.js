const http = new XMLHttpRequest()
let result = document.querySelector ("#result")

document.querySelector("#share").addEventListener
("click", () => {
findMyCoordinates()
})

function findMyCoordinates ()
{
if(navigator.geolocation)
{
}
else{
alert("Geolocation is not supported by your device")
}
}