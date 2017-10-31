$('body').on('load', DetectAndServe())

function getMobileOperatingSystem() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

  // Windows Phone must come first because its UA also contains "Android"
  if (/windows phone/i.test(userAgent)) {
    return "Windows Phone";
  }

  if (/android/i.test(userAgent)) {
    return "Android";
  }

  // iOS detection from: http://stackoverflow.com/a/9039885/177710
  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
    return "iOS";
  }

  return "unknown";
}

function DetectAndServe(){
  var link = null;
  if (getMobileOperatingSystem() == "Android") {
    link = "https://play.google.com/store/apps/details?id=org.tgs.civils.budget";
  }
  if (getMobileOperatingSystem() == "iOS") {
    link = "itms://itunes.apple.com/ua/app/apple-store/id1063027091?mt=8";
  }
  if (!!link) {
    var app = $('<div/>', {
      class: 'download'
    }).prependTo('body')

    $('<span/>', {
      class: "exit glyphicon glyphicon-remove",
      ariaHidden: true,
    }).appendTo(app)

    var img = $('<div/>', {
      class: 'img'
    }).appendTo(app)

    $('<img/>', {
      class: 'logo',
      src: '/images/thumbnail.png',
      alt: 'logo'
    }).appendTo(img)

    var description = $('<div/>', {
      class: "download-description"
    }).appendTo(app)

    var div = $('<div/>').appendTo(description)

    $('<p/>', {
      text: "Ви можете завантажити додаток для Вашого пристрою."
    }).appendTo(div)

    $('<a/>', {
      href: link,
      text: 'Скачати'
    }).appendTo(description)
    
    $('.exit').on('click', function () {
      $('.download').remove()
    })
  }
}