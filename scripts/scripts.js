$(document).ready(function(){
	$( "li.main_menu_element" ).hover(
		function() {
			$( this ).append( $( "<div class='hover_strip'></div>" ) );
		}, function() {
			$( this ).find( "div:last" ).remove();
		}
	);
}); 

$(document).ready(function() { 
	toggleArticle();
	handleForm();
	changeHeader.init();
});

	//easy scroll
$(document).ready(function() {
  function filterPath(string) {
  return string
    .replace(/^\//,'')
    .replace(/(index|default).[a-zA-Z]{3,4}$/,'')
    .replace(/\/$/,'');
  }
  var locationPath = filterPath(location.pathname);
  var scrollElem = scrollableElement('html', 'body');
  
  $('li.main_menu_element a[href*=#]').each(function() {
    var thisPath = filterPath(this.pathname) || locationPath;
    if (  locationPath == thisPath
    && (location.hostname == this.hostname || !this.hostname)
    && this.hash.replace(/#/,'') ) {
      var $target = $(this.hash), target = this.hash;
      if (target) {
        var targetOffset = $target.offset().top;
        $(this).click(function(event) {
          event.preventDefault();
          $(scrollElem).animate({scrollTop: targetOffset}, 800, function() {
            location.hash = target;
          });
        });
      }
    }
  });
   
	// use the first element that is "scrollable"
	function scrollableElement(els) {
	  for (var i = 0, argLength = arguments.length; i <argLength; i++) {
	    var el = arguments[i],
	        $scrollElement = $(el);
	    if ($scrollElement.scrollTop() > 0) {
	      return el;
	    } else {
	      $scrollElement.scrollTop(1);
	      var isScrollable = $scrollElement.scrollTop() > 0;
	      $scrollElement.scrollTop(0);
	      if (isScrollable) {
	        return el;
	      }
	    }
	  }
	  return [];
	}
    
});
	// toggle phone number
$(document).ready(function() {
	$('a.phone_btn_icon').click(function(e){
		e.preventDefault();
		$(this).next().fadeToggle();
	})
})

// open and close articles

var toggleArticle = function() {

	$('a.toggle_article_btn').click(function(e) {
		e.preventDefault();
		var btn  = $(this).data('open_btn'),
		 		elem = $(this).data('open_target'),
				like = btn + ' + a';
		$(elem).slideToggle(function() {
			$(like).fadeToggle()
			$(btn).fadeToggle()
		})
	})
}


// submit form via AJAX
var handleForm = function() {
	$('#h_form').submit(function(e) {
		e.preventDefault();

		var formObj = $(this),
			formUrl   = formObj.attr('action'),
			formData  = formObj.serialize(),
			actionurl = e.currentTarget.action;

	 	$.ajax({
		  url: formUrl,
		  type: 'post',
		  data: formData,
			dataType: 'json',
			beforeSend:function(){

			},
			complete:function(){

			},
			success: function(data) {
				$(".errors").html("");
				$(".submit_row h3").html(data.title)
				if (data.status == "green") {
					$(".form_result").addClass("green");
				}
				else {
					$(".form_result").addClass("red");
					for (var i in data.messages) {
		  			$(".errors").append('<div class="single_error">' + data.messages[i] + '</div>');
					}
				}			
			}	
		})
	})
}

// change header function
var changeHeader = {
	config : {
		docElem : document.documentElement,
		header : $('#main_menu_wrap'),
		didScroll : false,
		changeHeaderOn : 615
	},

	init : function(config) {
		$.extend(changeHeader.config, config);
		changeHeader.scrollPage();
		$( window ).scroll(function(e) {
			if( !changeHeader.config.didScroll ) {
				changeHeader.config.didScroll = true;
				setTimeout( changeHeader.scrollPage, 250 );
			}
		});
	},

	scrollPage : function() {
		var sy = changeHeader.scrollY();
		if ( sy >= changeHeader.config.changeHeaderOn ) {
			changeHeader.config.header.removeClass('static_menu_bar');
			changeHeader.config.header.addClass('fixed_menu_bar');
		}
		else {
			changeHeader.config.header.removeClass('fixed_menu_bar');
			changeHeader.config.header.addClass('static_menu_bar');
		}
		changeHeader.config.didScroll = false;
	},

	scrollY : function() {
		return window.pageYOffset || changeHeader.config.docElem.scrollTop;
	}

}





