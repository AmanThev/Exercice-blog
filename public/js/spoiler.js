var spoilers = document.querySelectorAll('.spoiler') 

var displaySpoiler = function (element) {
	var spanContent 		= document.createElement('span')
	var spanHeader 			= document.createElement('span')

	spanHeader.className 	= 'spoiler-header'
	spanHeader.textContent 	= 'Slide to display the spoiler'

	spanContent.className 	= 'spoiler-content' 
	spanContent.innerHTML 	= element.innerHTML

	element.innerHTML 		= ''

	element.appendChild(spanHeader)
	element.appendChild(spanContent)

	element.addEventListener('mouseover', function(){
		spanHeader.parentNode.removeChild(spanHeader)
		element.classList.add('visible')
		spanContent.classList.add('visible')
	})

	element.addEventListener('mouseout', function(){
		element.insertBefore(spanHeader, spanContent);
		element.classList.remove('visible')
		spanContent.classList.remove('visible')
	})
}

for(var i = 0; i < spoilers.length; i++){
	displaySpoiler(spoilers[i])
}