var body = $("body")

/*
 ajax 推出登陆 绑定id="user-logout"
 <a class="nav-link" rel="nofollow" id="user-logout" href="<?php echo url('user-logout');?>"><i class="icon-sign-out"></i>&nbsp;<?php echo lang('logout');?></a>
 */
body.on("click", "#user-logout", function () {
  var href = $(this).attr("href") || $(this).data("href")
  $.xpost(href, function (code, message) {
    if (code == 0) {
      $.alert(message).delay(1000).location()
    } else {
      alert(message)
    }
  })
  return false
})

/* 搜索使用 */
body.on("submit", "#form-search", function () {
  var jthis = $(this)
  var range = jthis.find('input[name="range"]:checked').val()
  range = range || jthis.find('input[name="range"]').val()
  var keyword = jthis.find('input[name="keyword"]').val()

  var fid = $("#btnCategoryText").attr("fid")
  var fidStr = ""
  if (fid) {
    var fname = $("#btnCategoryText").text()
    fidStr = "?fidFind=" + fid + "&fnameFind=" + fname
  }
  window.location = xn_bg.url(
    "operate-search-" + xn_bg.urlencode(keyword) + "-" + range + fidStr
  )
  return false
})

/*表单快捷键提交 CTRL+ENTER   / form quick submit*/
body.on("keyup", "form", function (e) {
  var jthis = $(this)
  if (
    (e.ctrlKey && (e.which == 13 || e.which == 10)) ||
    (e.altKey && e.which == 83)
  ) {
    jthis.trigger("submit")
    return false
  }
})

/*点击响应整行：方便手机浏览  / check response line*/
body.on("click", ".tap", function (e) {
  var href = $(this).attr("href") || $(this).data("href")
  if (e.target.nodeName == "LABEL" || e.target.nodeName == "INPUT") return true
  if ($(window).width() > 992) return
  if (e.ctrlKey) {
    window.open(href)
    return false
  } else {
    window.location = href
  }
})

/*点击响应整行：，但是不响应 checkbox 的点击  / check response line, without checkbox*/
$('.thread input[type="checkbox"]')
  .parents("td")
  .on("click", function (e) {
    e.stopPropagation()
  })

/*点击响应整行：导航栏下拉菜单   / check response line*/
body.on("click", "ul.nav > li", function (e) {
  var jthis = $(this)
  var href = jthis.children("a").attr("href")
  if (e.ctrlKey) {
    window.open(href)
    return false
  }
})

/*管理用户组*/
body.on("click", ".admin-manage-user", function () {
  var href = $(this).data("href")
  $.xpost(href, function (code, message) {
    if (code == 0) {
      $.alert(message).delay(1000).location()
    } else {
      $.alert(message).delay(2000).location()
    }
  })
  return false
})

$(function () {
  var nav = $("#nav-show")
  var remove =
    "d-lg-none position-fixed rounded-left bg-secondary d-flex align-items-center"
  var remove1 = "d-none d-lg-block"
  var remove2 = "sticky-top pt-2"
  var add = "shadow col-8 col-md-4 bg-white px-0"
  var add1 = "px-2"
  if ($("#btnCategoryText").attr("fid")) {
    $("#buttonCategory").addClass("categoryBtnClear")
  }
  /*菜单侧边滑出 .nav-block 控制在左右 */
  $(".button-show").click(function () {
    var jthis = $(this)
    var left = jthis.offset().left
    add += left ? " offset-4 offset-md-8" : ""
    jthis.css("display", "none")
    nav.before(
      '<div id="menu-wrap" style="overflow-x:hidden;overflow-y:auto;position:fixed;top:0;left:0;width:100%;height:100%;z-index:1031;background-color:#3a3b4566;"></div>'
    )
    jthis.removeClass(remove)
    /*nav.css({"position": "fixed", "top": "0", "bottom": "0", "right": "0", "margin-top": "3.625rem", "z-index": "1032"});*/
    nav.removeClass(remove1).addClass(add)
    nav.find(".post-sticky-top").removeClass(remove2).addClass(add1)
    /*nav.animate({right: ''}, 500);*/
    return false
  })

  /*菜单侧边收起弹出菜单*/
  $(".button-hide").click(function () {
    var jthis = $(this)
    var left = jthis.offset().left
    add += left ? " offset-3" : ""
    jthis.css("display", "none")
    var button_show = $(".button-show")
    button_show.addClass(remove)
    button_show.css("display", "block")
    $("#menu-wrap").remove()
    nav.removeClass(add).addClass(remove1)
    nav.find(".post-sticky-top").removeClass(add1).addClass(remove2)
    /*nav.animate({left: ''}, 500);*/
    return false
  })
})

/*tag*/
$(function () {
  var tag_input = $(".tag-input")
  tag_input.val("")

  $(document).on("keydown", ".tag-input", function (event) {
    var tag_input = $(this)
    var token = tag_input.parents(".tags").find(".tags-token")
    /* event.keyCode == 32 */
    if (event.keyCode == 13 || event.keyCode == 108 || event.keyCode == 188) {
      create_tag()
      return false
    }
    var str = tag_input.val().replace(/\s+/g, "")
    if (str.length == 0 && event.keyCode == 8) {
      if (token.length >= 1) {
        tag_input.parents(".tags").find(".tags-token:last").remove()
        get_tag_val(tag_input)
        return false
      }
    }
  })

  $(document).on("click", ".tags-token", function () {
    var it = $(this).parents(".tags")
    $(this).remove()
    var str = ""
    var token = it.find(".tags-token")
    if (token.length < 1) {
      it.find(".tags-val").val("")
      return false
    }
    for (var i = 0; i < token.length; i++) {
      str += token.eq(i).text() + ","
      it.find(".tags-val").val(str)
    }
  })

  tag_input.bind("input propertychange", function () {
    var str = $(this).val()
    /* || str.indexOf(' ') != -1 */
    if (str.indexOf(",") != -1 || str.indexOf("，") != -1) {
      create_tag()
      return false
    }
  })

  function create_tag() {
    var tag_input = $(".tag-input")
    /*var tag = tag_input.val().replace(/\s+/g, '');*/
    var tag = tag_input.val()
    var reg = new RegExp(
      "[`~!@#$^&*()=|{}:;,\\[\\].<>/?！￥…（）—【】‘；：”“。，、？%]",
      "g"
    )
    tag = tag.replace(reg, "")
    tag = tag.replace(/(^\s*)|(\s*$)/g, "")
    if (tag.length > 0) {
      var tags = $('input[name="tags"]').val()
      var arr = tags.split(",")
      if (arr.indexOf(tag) > -1) {
        tag_input.val("")
        return false
      }
      if (Object.count(arr) <= 5) {
        $(
          '<span class="tag tags-token" style="margin-right: 1rem;margin-bottom: .25rem;margin-top: .25rem;padding: .25rem .5rem;border: 1px solid #dddfeb;font-size: .8575rem;line-height: 1.5;border-radius: .2rem;">' +
            tag +
            "</span>"
        ).insertBefore(tag_input.parents(".tags").find(".tag-wrap"))
      }
      tag_input.val("")
      get_tag_val(tag_input)
    }
  }

  function get_tag_val(obj) {
    var str = ""
    var token = $(obj).parents(".tags").find(".tags-token")
    if (token.length < 1) {
      $(obj).parents(".tags").find(".tags-val").val("")
      return false
    }
    for (var i = 0; i < token.length; i++) {
      str += token.eq(i).text() + ","
      /*str = str.replace(/\s+/g, '');*/
      var reg = new RegExp(
        "[`~!@#$^&*()=|{}:;\\[\\].<>/?！￥…（）—【】‘；：”“。，、？%]",
        "g"
      )
      str = str.replace(reg, "")
      str = str.replace(/(^\s*)|(\s*$)/g, "")
      $(obj).parents(".tags").find(".tags-val").val(str)
    }
  }
})

/*
 确定框 / confirm / GET / POST
 <a href="1.php" data-confirm-text="确定删除？" class="confirm">删除</a>
 <a href="1.php" data-method="post" data-confirm-text="确定删除？" class="confirm">删除</a>
 */
body.on("click", "a.confirm", function () {
  var jthis = $(this)
  var text = jthis.data("confirm-text")
  $.confirm(text, function () {
    var method = xn_bg.strtolower(jthis.data("method"))
    var href = jthis.data("href") || jthis.attr("href")
    if ("post" == method) {
      $.xpost(href, function (code, message) {
        if (0 == code) {
          window.location.reload()
        } else {
          $.alert(message)
        }
      })
    } else {
      window.location = jthis.attr("href")
    }
  })
  return false
})

body.on("click", "a.ajax", function () {
  let jthis = $(this)
  let text = jthis.data("confirm-text") || ""

  if (text) {
    $.confirm(text, function () {
      well_click_ajax()
    })
  } else {
    well_click_ajax()
  }

  function well_click_ajax() {
    let method = xn_bg.strtolower(jthis.data("method"))
    let href = jthis.data("href") || jthis.attr("href")
    if ("post" == method) {
      let postdata = jthis.data("json")
      $.xpost(href, postdata, function (code, message) {
        if (0 == code) {
          if (message.text) {
            jthis.html(message.text)
            if (message.url) jthis.attr("href", message.url) /*url*/
            if (message.method)
              jthis.attr("data-method", message.method) /*data-method*/
            if (message.modal)
              jthis.attr("data-method", message.modal) /*data-modal-title*/
          } else if (undefined == message.text) {
            window.location.reload()
          } else if (message) {
            $.alert(message)
            setTimeout(function () {
              window.location.reload()
            }, 1000)
          }
        } else if ("url" == code) {
          window.location = message
        } else {
          $.alert(message)
        }
      })
    } else {
      window.location = jthis.attr("href")
    }
  }

  return false
})

/*选中所有 / check all
 <input class="checkall" data-target=".tid" />*/
body.on("click", "input.checkall", function () {
  var jthis = $(this)
  var target = jthis.data("target")
  jtarget = $(target)
  jtarget.prop("checked", this.checked)
})

/*引用 / Quote*/
body.on("click", ".well_reply", function () {
  var jthis = $(this)
  var tid = jthis.data("tid")
  var pid = jthis.data("pid")
  var jmessage = $("#message")
  var jli = jthis.closest(".post")
  var jpostlist = jli.closest(".postlist")
  var jadvanced_reply = $("#advanced_reply")
  var jform = $("#form")
  if (jli.hasClass("quote")) {
    jli.removeClass("quote")
    jform.find('input[name="quotepid"]').val(0)
    jadvanced_reply.attr("href", xn_bg.url("comment-create-" + tid))
  } else {
    jpostlist.find(".post").removeClass("quote")
    jli.addClass("quote")
    jform.find('input[name="quotepid"]').val(pid)
    jadvanced_reply.attr("href", xn_bg.url("comment-create-" + tid + "-" + pid))
  }
  jmessage.focus()
  return false
})

/*引用 / Quote*/
body.on("click", ".post_reply", function () {
  var jthis = $(this)
  var tid = jthis.data("tid")
  var pid = jthis.data("pid")
  var jmessage = $("#message")
  var jli = jthis.closest(".post")
  var jpostlist = jli.closest(".postlist")
  var jadvanced_reply = $("#advanced_reply")
  var jform = $("#quick_reply_form")
  if (jli.hasClass("quote")) {
    jli.removeClass("quote")
    jform.find('input[name="quotepid"]').val(0)
    jadvanced_reply.attr("href", xn_bg.url("post-create-" + tid))
  } else {
    jpostlist.find(".post").removeClass("quote")
    jli.addClass("quote")
    jform.find('input[name="quotepid"]').val(pid)
    jadvanced_reply.attr("href", xn_bg.url("post-create-" + tid + "-0-" + pid))
  }
  jmessage.focus()
  return false
})

/* 删除 / Delete post*/
body.on("click", ".post_delete", function () {
  var jthis = $(this)
  var href = jthis.data("href")
  if (window.confirm(lang.confirm_delete)) {
    $.xpost(href, { safe_token: safe_token }, function (code, message) {
      var isfirst = jthis.attr("isfirst")
      if (code == 0) {
        if (isfirst == 1) {
          window.location = jthis.attr("forum-url")
        } else {
          // 删掉楼层
          jthis.parents(".post").remove()
          // 回复数 -1
          var jposts = $(".posts")
          jposts.html(xn_bg.intval(jposts.html()) - 1)
        }
      } else {
        $.alert(message)
      }
    })
  }
  return false
})

body.on("click", ".install, .uninstall", function () {
  var jthis = $(this)
  var href = jthis.data("href") || jthis.attr("href")
  $.xpost(href, function (code, message) {
    if (code == 0) {
      $.alert(message).delay(1000).location()
    } else {
      $.alert(message)
    }
  })
  return false
})

$(function () {
  var body = $("body")
  body.on("click", "#but-sidebar-toggle", function () {
    var toggle = $("#sidebar-toggle")
    toggle.toggleClass("position-fixed d-none d-lg-block")
    toggle.collapse("hide")
    toggle.css("z-index", "999")
  })

  var scroll_top = function (scroll_distance) {
    if (scroll_distance > 100) {
      $(".scroll-to-top").fadeIn()
      $(".scroll-to-bottom").fadeOut()
    } else {
      $(".scroll-to-top").fadeOut()
      $(".scroll-to-bottom").fadeIn()
    }
  }

  /* Scroll to top button appear */
  var wrapper = $("#content-wrapper")
  if (wrapper.length > 0) {
    wrapper.on("scroll", function () {
      scroll_top($(this).scrollTop())
    })
  } else {
    $(document).on("scroll", function () {
      scroll_top($(this).scrollTop())
    })
  }

  /* scroll to top */
  body.on("click", "a.scroll-to-top", function (e) {
    $("html, body, #content-wrapper").animate({ scrollTop: 0 }, 500)
    e.preventDefault()
  })

  /* scroll to bottom */
  body.on("click", "a.scroll-to-bottom", function (e) {
    var height = $("#body").height() || $("body").height()
    $("html, body, #content-wrapper").animate({ scrollTop: height }, 500)
    e.preventDefault()
  })
})

/* post 数组格式化为 get 请求参数 */
function well_params_fmt(data) {
  var arr = []
  for (var name in data) {
    arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]))
  }
  arr.push(("v=" + Math.random()).replace(".", ""))
  return arr.join("&")
}

/*
滚动到窗口可视区域元素位置中间下方
well_set_top('id', Element)
*/
function well_set_top(Type, Element) {
  let scrollTop = document.documentElement.scrollTop
  let scrollHeight = document.body.scrollHeight
  let innerHeight = window.innerHeight
  let from =
    "id" === Type
      ? document.getElementById(Element)
      : document.getElementsByClassName(Element)
  /* 距离顶部距离 */
  let top = from.getBoundingClientRect().top
  /* 元素高度 */
  let height = from.getBoundingClientRect().height
  _height = top - innerHeight / 2 - height
  if (top > innerHeight) {
    _height = innerHeight / 2
  }

  let x = from.offsetTop + _height

  /* 判断是否在移动端打开 */
  /*let u = navigator.userAgent;
    if (u.match(/AppleWebKit.*Mobile.*!/)) {
        x = form.offsetTop + _height;
    }*/
  let timer = setInterval(function () {
    document.documentElement.scrollTop += _height
    if (document.documentElement.scrollTop >= x) {
      clearInterval(timer)
    }
  }, 50)

  let timer_1 = setInterval(function () {
    window.pageYOffset += _height
    if (window.pageYOffset >= x) {
      clearInterval(timer_1)
    }
  }, 50)

  let timer_2 = setInterval(function () {
    document.body.scrollTop += _height
    if (document.body.scrollTop >= x) {
      clearInterval(timer_2)
    }
  }, 50)
}

/*
获取表单值 调用方法
formId
format:0对象{'key':'value'} 1字符串key=value
console.log(well_serialize_form('form'));
console.log(well_serialize_form('form', 1));
*/
function well_serialize_form(formId, format) {
  let form = document.getElementById(formId)
  if (form && "FORM" != form.tagName) {
    let parent = form.parentNode
    while ("FORM" != parent.tagName) {
      parent = parent.parentNode
    }
    if (!parent) ""
    formId = parent.id
  } else {
    formId = form.id
    if (!formId) return ""
  }

  format = format || 0
  let elements = well_get_elements(formId)
  let queryComponents = new Array()
  let length = elements.length
  for (let i = 0; i < length; ++i) {
    let queryComponent = well_serialize_element(elements[i], format)
    if (queryComponent) queryComponents.push(queryComponent)
  }

  if (format) return queryComponents.join("&")

  let ojb = {}
  let len = queryComponents.length
  if (!len) return ojb

  for (let i = 0; i < len; ++i) {
    ojb[queryComponents[i][0]] = queryComponents[i][1]
  }

  return ojb
}

/*
获取指定form中的所有的<input>对象
暂时不支持表单数组name="a[]"
*/
function well_get_elements(formId) {
  let form = document.getElementById(formId)
  if (!form) return ""
  let elements = new Array()
  let tagInputs = form.getElementsByTagName("input")
  for (let i = 0; i < tagInputs.length; ++i) {
    elements.push(tagInputs[i])
  }

  let tagSelects = form.getElementsByTagName("select")
  for (let i = 0; i < tagSelects.length; ++i) {
    elements.push(tagSelects[i])
  }

  let tagTextareas = form.getElementsByTagName("textarea")
  for (let i = 0; i < tagTextareas.length; ++i) {
    elements.push(tagTextareas[i])
  }

  return elements
}

/* 组合URL 0数组'key':'value' 1字符串key=value */
function well_serialize_element(element, format) {
  format = format || 0
  let method = element.tagName.toLowerCase()
  let parameter

  if ("select" == method) parameter = [element.name, element.value]

  switch (element.type.toLowerCase()) {
    case "submit":
    case "hidden":
    case "password":
    case "text":
    case "date":
    case "textarea":
      parameter = [element.name, element.value]
      break
    case "checkbox":
    case "radio":
      if (element.checked) {
        parameter = [element.name, element.value]
      }
      break
  }

  if (parameter) {
    //let key = encodeURIComponent(parameter[0]);
    let key = parameter[0]

    if (0 == key.length) return

    if (parameter[1].constructor != Array) parameter[1] = [parameter[1]]

    let results = new Array()
    let values = parameter[1]
    let length = values.length
    for (let i = 0; i < length; ++i) {
      if (format) {
        results.push(key + "=" + values[i])
      } else {
        results = [key, values[i]]
      }
    }

    if (format) {
      return results.join("&")
    } else {
      return results
    }
  }
}

/*
 * body = Element
 * options = {'title': 'title', 'timeout': '1', 'size': '', 'width': '550px', 'fixed': 'bottom', 'bg': 'white', 'screen': 'black'};
 *
 * title 标题
 * timeout x秒关闭 0点击关闭 -1自行使用代码关闭
 * size 模态框大小CSS 定义的class / bootstrap 可以使用 modal-dialog modal-md
 * width 限制模态框宽度 size和width同时存在时，使用width 550px
 * fixed 默认居中center 从底部弹出bottom
 * screen 弹窗全屏背景 默认透明 black 黑色60%透明度
 * bg 弹窗背景 默认黑色60%透明度 white or black
 * rounded 边框角度，默认0.25rem 圆角
 * */
$.modal = function (body, options) {
  let w_modal = document.getElementById("w-modal")
  if (w_modal) w_modal.parentNode.removeChild(w_modal)

  options = options || {
    title: "",
    timeout: "1",
    size: "",
    width: "550px",
    fixed: "center",
    screen: "",
    bg: "rgb(0 0 0 / 60%)",
    rounded: "0.25rem",
  }
  if (options.size && options.width) options.size = ""

  if ("white" == options.bg) {
    options.bg = "#FFFFFF"
    font_bg = "rgb(0 0 0 / 100%)"
  } else if ("black" == options.bg) {
    options.bg = "rgb(0 0 0 / 60%)"
    font_bg = "#FFFFFF"
  } else {
    options.bg = "rgb(0 0 0 / 60%)"
    font_bg = "#FFFFFF"
  }

  let styleCode = ""
  let header = ""
  if (options.title || 0 == options.timeout) {
    let title = "&nbsp;"
    if (options.title) {
      title =
        '<div id="w-title" style="position: relative;margin: .5rem .5rem;line-height: 1.3;font-weight: bold;font-size: 1.05rem;color: ' +
        font_bg +
        ';">' +
        options.title +
        "</div>"
    }

    let close = ""
    if (0 == options.timeout) {
      close =
        '<span id="w-modal-close" style="position: relative;padding: .5rem .5rem;float: right;font-size: 1.5rem;font-weight: 700;cursor:pointer;color: ' +
        font_bg +
        ';">&times;</span>'
    }

    header =
      '\
        <div id="w-modal-header" style="display: flex;position: relative;width: 100%;align-items: flex-start;justify-content: space-between;line-height: .8;padding: 0.5rem 0;">\
            ' +
      title +
      "\
            " +
      close +
      "\
		</div>"
  }

  if (!options.fixed) options.fixed = "center"
  if (!options.rounded) options.rounded = "0.25rem"

  if ("top" == options.fixed) {
    fixed =
      "position:fixed;top:0;left:0;visibility:visible;animation: modal-fadein .5s;"
    radius =
      "border-bottom-left-radius:" +
      options.rounded +
      ";border-bottom-right-radius:" +
      options.rounded +
      ";"
    options.width = "100%"
    styleCode +=
      "@keyframes modal-fadein { from{opacity:0;top:0;} to{opacity:1;top:0;}}"
  } else if ("center" == options.fixed) {
    /*let Width = window.screen.availWidth;
        if (Width > 800) {
            maxWidth = 'calc(100% - 30px)';
        } else {
            maxWidth = '100%';
        }*/
    let maxWidth = "calc(100% - 30px)"
    fixed =
      "position: relative;top:50%;left:50%;max-height:calc(100% - 30px);max-width:" +
      maxWidth +
      ";transform:translate(-50%,-50%);"
    radius = "border-radius: " + options.rounded + ";"
  } else if ("bottom" == options.fixed) {
    fixed =
      "position:fixed;bottom:0;left:0;visibility:visible;animation: modal-fadein .5s;"
    radius =
      "border-top-left-radius:" +
      options.rounded +
      ";border-top-right-radius:" +
      options.rounded +
      ";"
    options.width = "100%"
    styleCode +=
      "@keyframes modal-fadein { from{opacity:0;bottom:0;} to{opacity:1;bottom:0;}}"
  }

  let style = "<style>" + styleCode + "</style>"

  let screen = ""
  if (options.screen && "black" == options.screen) {
    screen = "background-color: rgb(0 0 0 / 60%);"
  }

  const s =
    "\
    " +
    style +
    '\
    <div style="display: block;overflow-x: hidden;overflow-y: hidden;position: fixed;top: 0;left: 0;z-index: 1050;width: 100%;height: 100%;' +
    screen +
    '">\
        <div id="w-modal-dialog" style="flex-direction: column;overflow-x: hidden;overflow-y: hidden;margin:0 !important;width: 100%;' +
    fixed +
    '">\
            <div id="w-wrap" class="' +
    options.size +
    '" style="position: relative;margin: 0 auto;max-width:' +
    options.width +
    ";font-size: 1.2rem;background-color: " +
    options.bg +
    ";color: " +
    font_bg +
    ";pointer-events: auto !important;overflow-x: hidden;overflow-y: hidden;" +
    radius +
    '">\
            <div id="w-modal-content" style="display: block;position: relative;display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;padding: 0 .5rem;overflow-x: hidden;overflow-y: auto;width: 100%;">\
                ' +
    header +
    '\
                <div id = "w-modal-body" style = "display: block;display: -ms-flexbox;display: flex;position: relative;-ms-flex-direction: column;flex-direction: column;word-wrap: break-word;-ms-flex: 1 1 auto;flex: 1 1 auto;width: 100%;" >' +
    body +
    "</div>\
            </div>\
        </div>\
    </div>"

  let modal = document.createElement("div")
  modal.id = "w-modal"
  modal.innerHTML = s
  let jmodal = document.body.insertBefore(modal, document.body.lastElementChild)
  if (typeof options.timeout) {
    w_modal = document.getElementById("w-modal")
    console.log(w_modal)
    if (options.timeout > 0) {
      setTimeout(function () {
        w_modal.parentNode.removeChild(w_modal)
      }, options.timeout * 1000)
    } else if (0 == options.timeout) {
      w_close = document.getElementById("w-modal-close")
      if (w_close) {
        w_close.addEventListener("click", function (e) {
          w_modal.parentNode.removeChild(w_modal)
          e.stopPropagation()
        })
      }
    }
  }

  return jmodal
}

/*
options = {'title': '标题可空', 'timeout': 0, 'size': '定义的class', 'width': '550px', 'fixed': 'center or bottom', 'screen': 'black 黑色背景', 'rounded': '0.25rem 圆角', 'bg': 'white or black 默认黑色60%透明度'}
*/
$.ajaxModal = function (url, callback, arg, options) {
  options = options || {
    title: ".",
    timeout: 0,
    size: "",
    width: "550px",
    fixed: "center",
    screen: "",
    rounded: "",
  }
  if (0 != options.timeout) options.timeout = 0
  if (!options.size && !options.width) options.width = "550px"

  let jmodal = $.modal(
    '<div style="text-align: center;padding-bottom: 1.5rem;padding-top: .5rem;">Loading...</div>',
    options
  )

  jmodal.querySelector('[id="w-title"]').innerHTML = options.title

  /*ajax 加载内容*/
  $.xget(url, function (code, message) {
    /*对页面 html 进行解析*/
    if (code == -101) {
      var r = xn_bg.get_title_body_script_css(message)
      jmodal.querySelector('[id="w-modal-body"]').innerHTML = r.body
    } else {
      jmodal.querySelector('[id="w-modal-body"]').innerHTML =
        '<div style="text-align: center;padding-bottom: 1.5rem;padding-top: .5rem;">' +
        message +
        "</div>"
      return
    }
    /*eval script, css*/
    xn_bg.eval_stylesheet(r.stylesheet_links)
    jmodal.script_sections = r.script_sections
    if (r.script_srcs.length > 0) {
      $.require(r.script_srcs, function () {
        xn_bg.eval_script(r.script_sections, {
          jmodal: jmodal,
          callback: callback,
          arg: arg,
        })
      })
    } else {
      xn_bg.eval_script(r.script_sections, {
        jmodal: jmodal,
        callback: callback,
        arg: arg,
      })
    }
  })

  return jmodal
}

/*
modal-width 和 modal-size 同时存在，优先使用 modal-width

<button id="button1" class="w-ajax-modal btn btn-primary" modal-url="user-login.htm" modal-title="用户登录" modal-arg="xxx" modal-callback="login_success_callback" modal-width="550px" modal-size="md" modal-fixed="bottom" modal-bg="white" modal-rounded="1rem" modal-screen="black">登陆</button>

<a class="w-ajax-modal nav-link" rel="nofollow" modal-title="<?php echo lang('login');?>" modal-arg="xxx" modal-callback="login_success_callback" modal-width="550px" modal-size="md" modal-fixed="bottom" modal-bg="white" modal-screen="black" modal-rounded="1rem" href="<?php echo url('user-login');?>"><i class="icon-user"></i>&nbsp;<?php echo lang('login');?></a>
*/
$(function () {
  var modalList = document.getElementsByClassName("w-ajax-modal")
  var length = modalList.length
  for (var i = 0; i < length; ++i) {
    modalList[i].onclick = function (e) {
      let jthis = this
      let url = jthis.getAttribute("modal-url") || jthis.getAttribute("href")
      let title = jthis.getAttribute("modal-title")
      if (!title) title = ""
      let arg = jthis.getAttribute("modal-arg")
      if (!arg) arg = ""
      let callback_str = jthis.getAttribute("modal-callback")
      let callback = callback_str ? window[callback_str] : ""
      let width = jthis.getAttribute("modal-width")
      if (!width) width = ""
      let size = jthis.getAttribute("modal-size")
      if (!size) size = ""
      let fixed = jthis.getAttribute("modal-fixed")
      if (!fixed) fixed = ""
      let bg = jthis.getAttribute("modal-bg")
      if (!bg) bg = ""
      let screen = jthis.getAttribute("modal-screen")
      if (!screen) screen = ""
      let rounded = jthis.getAttribute("modal-rounded")
      if (!rounded) rounded = ""
      let options = {
        title: title,
        timeout: 0,
        size: size,
        width: width,
        fixed: fixed,
        screen: screen,
        bg: bg,
        rounded: rounded,
      }
      $.ajaxModal(url, callback, arg, options)
      e.stopPropagation()
      return false
    }
  }
})

/*二位数组 依据 key 排序
 * asc false升序 true降序
 * */
arrListMultiSort = function (arrList, asc) {
  let newKeys = Object.keys(arrList).sort(function (a, b) {
    return parseInt(arrList[a].num) - parseInt(arrList[b].num)
  })

  if (asc) newKeys.reverse()

  var arr = []
  for (let i in newKeys) {
    arr.push(arrList[newKeys[i]])
  }

  /*console.log(arr);*/
  return arr
}

/**
 * number_format
 * @param number 传进来的数,
 * @param bit 保留的小数位,默认保留两位小数,
 * @param sign 为整数位间隔符号,默认为空格
 * @param gapnum 为整数位每几位间隔,默认为3位一隔
 * @type arguments的作用：arguments[0] == number(之一)
 */
number_format = function (number, bit, sign, gapnum) {
  /*设置接收参数的默认值*/
  bit = arguments[1] ? arguments[1] : 2
  sign = arguments[2] ? arguments[2] : ""
  gapnum = arguments[3] ? arguments[3] : 3
  var str = ""

  number = number.toFixed(bit) /*格式化*/
  realnum = number.split(".")[0] /*整数位(使用小数点分割整数和小数部分)*/
  decimal = number.split(".")[1] /*小数位*/
  realnumarr =
    realnum.split("") /*将整数位逐位放进数组 ["1", "2", "3", "4", "5", "6"]*/

  /*把整数部分从右往左拼接，每bit位添加一个sign符号*/
  for (var i = 1; i <= realnumarr.length; i++) {
    str = realnumarr[realnumarr.length - i] + str
    if (i % gapnum == 0) {
      str = sign + str /*每隔gapnum位前面加指定符号*/
    }
  }

  /*当遇到 gapnum 的倍数的时候，会出现比如 ",123",这种情况，所以要去掉最前面的 sign*/
  str = realnum.length % gapnum == 0 ? str.substr(1) : str
  /*重新拼接实数部分和小数位*/
  realnum = str + "." + decimal
  return realnum
}

format_number = function (number) {
  number = parseInt(number)
  return number > 1000
    ? (number > 1100
        ? number_format(number / 1000, 1)
        : parseInt($number / 1000)) + "K+"
    : number
}

/**
 * 获取客户端信息
 */
get_device = function () {
  var userAgent = navigator.userAgent
  var Agents = new Array(
    "Android",
    "iPhone",
    "SymbianOS",
    "Windows Phone",
    "iPad",
    "iPod"
  )
  var agentinfo = null
  for (var i = 0; i < Agents.length; i++) {
    if (userAgent.indexOf(Agents[i]) > 0) {
      agentinfo = userAgent
      break
    }
  }
  if (agentinfo) {
    return agentinfo
  } else {
    return "PC"
  }
}

$("#clearSelect").click(function (event) {
  event.stopPropagation()
  $("#buttonCategory").removeClass("categoryBtnClear")
  $("#buttonCategory").attr("title", "")
  $("#btnCategoryText").text("All category")
  $("#btnCategoryText").attr("fid", "")
})
var buttonCategory
$("#buttonCategory").click(function () {
  if (buttonCategory) {
    return $("#categoryAllFindBox").slideDown()
  }
  buttonCategory = 1
  selectMainBox()
})

var isOpenSearchTime
$("#openSearchBtn").click(function () {
  $("#searchBox").addClass("isOpenSearch")
})
$("#searchBox").mouseout(function () {
  closeSearch()
})
$("#searchBox").mousemove(function () {
  clearTimeout(isOpenSearchTime)
})
$("#inputKeyword").keyup(function () {
  clearTimeout(isOpenSearchTime)
})
$("#inputKeyword").focus(function () {
  setTimeout(() => {
    clearTimeout(isOpenSearchTime)
  }, 1000)
})
$("#closeSearchBox").click(function () {
  $("#searchBox").removeClass("isOpenSearch")
})
function closeSearch() {
  // console.log(new Date().getTime())
  isOpenSearchTime = setTimeout(() => {
    $("#searchBox").removeClass("isOpenSearch")
  }, 9000)
}

function selectMainBox() {
  var list = [
    {
      fid: 497,
      fnm: "Shop",
      child: [
        { fid: 498, fnm: "Handbags" },
        { fid: 499, fnm: "Shoes" },
        { fid: 506, fnm: "watch" },
        { fid: 500, fnm: "Accessories" },
        { fid: 505, fnm: "BELT" },
        { fid: 504, fnm: "NECKLACE" },
        { fid: 501, fnm: "SUNGLASSES" },
        { fid: 502, fnm: "BRACELET" },
        { fid: 507, fnm: "Clothings" },
        { fid: 503, fnm: "Uncategorized" },
      ],
    },

    {
      fid: 1,
      fnm: "Women's Bags",
      child: [
        { fid: 21, fnm: "𝗔𝗹𝗲𝐱𝐚𝗻𝗱𝗲𝐫 𝗪𝗮𝗻𝗴" },
        { fid: 8, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 15, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 16, fnm: "𝐁𝘂𝗹𝗴𝐚𝗿𝐢" },
        { fid: 11, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 10, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 4, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 22, fnm: "𝗖𝗵𝐥𝗼é" },
        { fid: 17, fnm: "𝐃𝐞𝐥𝐯𝗮𝐮𝘅" },
        { fid: 5, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 14, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 20, fnm: "𝐆𝐨𝐲𝐚𝐫𝗱" },
        { fid: 3, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 6, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 13, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 23, fnm: "𝗟𝗼𝐫𝗼  𝐏𝗶𝗮n𝗮" },
        { fid: 2, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 19, fnm: "𝐌𝗖𝐌" },
        { fid: 12, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 9, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 18, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 7, fnm: "𝗬𝐒𝗟" },
      ],
    },

    {
      fid: 24,
      fnm: "Men's Bags",
      child: [
        { fid: 33, fnm: "𝗕𝐚𝗹𝐥𝘆" },
        { fid: 31, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 30, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 27, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 29, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 32, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 26, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 25, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 35, fnm: "𝐌𝗖𝐌" },
        { fid: 28, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 34, fnm: "𝐓𝗵𝐨𝗺 𝐁𝐫𝐨𝐰𝗻𝗲" },
      ],
    },

    {
      fid: 40,
      fnm: "Wallets",
      child: [
        { fid: 54, fnm: "𝗕𝐚𝗹𝐥𝘆" },
        { fid: 47, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 49, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 43, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 44, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 50, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 51, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 42, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 41, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 46, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 48, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 52, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 45, fnm: "𝗬𝐒𝗟" },
      ],
    },

    {
      fid: 36,
      fnm: "Travel Luggage",
      child: [
        { fid: 39, fnm: "Backpacks" },
        { fid: 38, fnm: "Travel Bags" },
        { fid: 37, fnm: "Trolley Cases" },
      ],
    },

    {
      fid: 55,
      fnm: "Women's Clothing",
      child: [
        { fid: 78, fnm: "𝐀𝐜𝐧𝗲 𝗦𝘁𝐮𝗱𝐢𝗼𝐬" },
        { fid: 68, fnm: "𝗔𝗹𝗲𝐱𝐚𝗻𝗱𝗲𝐫 𝗪𝗮𝗻𝗴" },
        { fid: 79, fnm: "𝗔𝐫𝗺𝐚𝗻𝐢" },
        { fid: 64, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 81, fnm: "𝗕𝗮𝐥𝐦𝐚𝗶𝐧" },
        { fid: 71, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 61, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 87, fnm: "𝗖𝐚𝐬𝐡𝐦𝗲𝗿𝗲 𝐂𝗼𝐚𝘁𝐬, 𝐅𝐮𝗿𝘀" },
        { fid: 67, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 56, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 73, fnm: "𝗖𝗵𝐥𝗼é" },
        { fid: 72, fnm: "𝗖𝐡𝗿𝗼𝗺𝐞 𝐇𝐞𝐚𝐫𝘁𝐬" },
        { fid: 58, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 84, fnm: "𝗗𝐨𝐥𝗰𝗲 &amp; 𝗚𝗮𝐛𝗯𝗮𝐧𝐚" },
        { fid: 66, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 69, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 88, fnm: "𝐆𝐨𝗼𝘀𝐞 𝗗𝐨𝐰𝗻 𝗝𝗮𝗰𝐤𝗲𝘁 Women's" },
        { fid: 57, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 59, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 80, fnm: "𝐊𝐞𝐧𝘇𝐨" },
        { fid: 63, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 370, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 83, fnm: "𝐌𝐚𝐱𝐌𝗮𝗿𝐚" },
        { fid: 65, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 74, fnm: "𝗠𝐨𝗻𝐜𝗹𝗲𝐫" },
        { fid: 85, fnm: "𝗠𝐨𝘀𝗰𝗵𝗶𝗻𝐨" },
        { fid: 82, fnm: "𝗢𝐟𝗳𝐖𝗵𝐢𝐭𝐞" },
        { fid: 90, fnm: "𝐎𝐭𝐡𝗲𝗿 𝐁𝗿𝐚𝗻𝐝𝘀 𝐨𝗳" },
        { fid: 62, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 89, fnm: "𝗣𝗿𝐞𝐦𝐢𝐮𝗺 Women's Down 𝐉𝗮𝐜𝐤𝗲𝘁𝘀" },
        { fid: 76, fnm: "𝗧𝐡𝗲 𝗡𝗼𝗿𝐭𝐡 𝗙𝗮𝐜𝗲" },
        { fid: 75, fnm: "𝐓𝗵𝐨𝗺 𝐁𝐫𝐨𝐰𝗻𝗲" },
        { fid: 86, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 70, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 60, fnm: "𝗬𝐒𝗟" },
        { fid: 77, fnm: "𝗭𝐢𝐦𝐦𝐞𝗿𝐦𝐚𝐧𝐧" },
      ],
    },

    {
      fid: 91,
      fnm: "Men's Clothing",
      child: [
        { fid: 101, fnm: "𝗔𝐫𝗺𝐚𝗻𝐢" },
        { fid: 116, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 109, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 112, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 94, fnm: "𝗖𝗮𝗻𝐚𝗱𝗮 𝗚𝐨𝐨𝐬𝐞" },
        { fid: 111, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 119, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 102, fnm: "𝗖𝐡𝗿𝗼𝗺𝐞 𝐇𝐞𝐚𝐫𝘁𝐬" },
        { fid: 120, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 97, fnm: "𝗗𝐨𝐥𝗰𝗲 &amp; 𝗚𝗮𝐛𝗯𝗮𝐧𝐚" },
        { fid: 115, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 104, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 121, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 118, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 99, fnm: "𝐊𝐞𝐧𝘇𝐨" },
        { fid: 114, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 122, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 105, fnm: "𝐌𝐚𝘀𝘁𝐞𝐫𝐦𝗶𝗻𝐝 𝐉𝗮𝗽𝗮𝐧" },
        { fid: 110, fnm: "𝗠𝐨𝗻𝐜𝗹𝗲𝐫" },
        { fid: 98, fnm: "𝗠𝐨𝘀𝗰𝗵𝗶𝗻𝐨" },
        { fid: 100, fnm: "𝐎𝗳𝐟-𝗪𝗵𝐢𝘁𝗲" },
        { fid: 95, fnm: "𝐎𝐭𝐡𝗲𝗿 𝐁𝗿𝐚𝗻𝐝𝘀 𝐨𝗳" },
        { fid: 117, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 93, fnm: "𝗣𝗿𝐞𝐦𝐢𝐮𝗺 Men's Down 𝐉𝗮𝐜𝐤𝗲𝘁𝘀" },
        { fid: 123, fnm: "𝐒𝘂𝗺𝗺𝐞𝐫 𝐒𝗵𝗼𝗿𝐭𝘀" },
        { fid: 107, fnm: "𝗧𝐡𝗲 𝗡𝗼𝗿𝐭𝐡 𝗙𝗮𝐜𝗲" },
        { fid: 108, fnm: "𝐓𝗵𝐨𝗺 𝐁𝐫𝐨𝐰𝗻𝗲" },
        { fid: 96, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 103, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 113, fnm: "𝗬𝐒𝗟" },
        { fid: 106, fnm: "𝗭𝐞𝗴𝐧𝐚" },
      ],
    },

    {
      fid: 124,
      fnm: "Women's Shoes",
      child: [
        { fid: 168, fnm: "𝐀𝐈𝗥 𝐉𝐎𝗥𝐃𝐀𝗡" },
        { fid: 167, fnm: "𝗔𝗹𝗲𝐱𝐚𝗻𝗱𝗲𝐫 𝗪𝗮𝗻𝗴" },
        { fid: 166, fnm: "𝐀𝐦𝗶𝗿𝗶" },
        { fid: 165, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 164, fnm: "𝗕𝐚𝗹𝐥𝘆" },
        { fid: 163, fnm: "𝗕𝗮𝐥𝐦𝐚𝗶𝐧" },
        { fid: 162, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 161, fnm: "𝐁𝗿𝘂𝐧𝗲𝗹𝗹𝐨 𝗖𝘂𝗰𝗶𝗻𝐞𝗹𝗹𝗶" },
        { fid: 160, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 159, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 158, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 157, fnm: "𝗖𝗵𝐥𝗼é" },
        { fid: 156, fnm: "𝗖𝗵𝐫𝗶𝐬𝐭𝐢𝐚𝗻 𝗟𝐨𝐮𝐛𝗼𝘂𝘁𝗶𝗻" },
        { fid: 155, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 154, fnm: "𝗗𝐨𝐥𝗰𝗲 &amp; 𝗚𝗮𝐛𝗯𝗮𝐧𝐚" },
        { fid: 153, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 152, fnm: "𝗙𝐞𝐫𝐫𝐚𝐠𝗮𝐦𝐨" },
        { fid: 151, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 150, fnm: "𝐆𝗼𝗹𝗱𝗲𝐧 𝐆𝗼𝐨𝐬𝗲" },
        { fid: 149, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 147, fnm: "𝐇𝗢𝐆𝐀𝐍" },
        { fid: 148, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 146, fnm: "𝐉𝐈𝗟 𝐒𝐀𝐍𝐃𝐄𝗥" },
        { fid: 145, fnm: "𝐉𝗶𝗺𝗺𝐲 𝗖𝗵𝐨𝗼" },
        { fid: 144, fnm: "𝐋𝐀𝗡𝗩𝗜𝐍" },
        { fid: 143, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 142, fnm: "𝗟𝗼𝐫𝗼  𝐏𝗶𝗮n𝗮" },
        { fid: 141, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 140, fnm: "𝗠𝐚𝗶𝘀𝗼𝗻 𝐌𝗮𝗿𝗴𝐢𝗲𝗹𝐚" },
        { fid: 139, fnm: "𝗠𝗮𝗻𝐨𝐥𝐨 𝗕𝐥𝗮𝐡𝐧𝗶𝐤" },
        { fid: 138, fnm: "𝗠𝗮𝗿𝐧𝐢" },
        { fid: 137, fnm: "𝐌𝐜𝗤𝘂𝐞𝐞𝗻" },
        { fid: 136, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 134, fnm: "𝐎𝐭𝐡𝗲𝗿 𝐁𝗿𝐚𝗻𝐝𝘀 𝐨𝗳" },
        { fid: 133, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 132, fnm: "𝐑𝐢𝗰𝗸 𝗢𝐰𝐞𝐧𝐬" },
        { fid: 131, fnm: "𝗥𝗼𝗴𝐞𝗿 𝗩𝐢𝘃𝗶𝗲𝐫" },
        { fid: 130, fnm: "𝐒𝗲𝗿𝗴𝐢𝐨 𝗥𝗼𝐬𝐬i" },
        { fid: 129, fnm: "𝐓𝗢𝗗𝐒" },
        { fid: 128, fnm: "𝐔𝗚𝗚" },
        { fid: 127, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 371, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 125, fnm: "𝗬𝐒𝗟" },
        { fid: 126, fnm: "𝐘𝐞𝗲𝘇𝘆" },
      ],
    },

    {
      fid: 169,
      fnm: "Men's Shoes",
      child: [
        { fid: 203, fnm: "𝐀𝗝" },
        { fid: 202, fnm: "𝗔𝐫𝗺𝐚𝗻𝐢" },
        { fid: 201, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 200, fnm: "𝗕𝐚𝗹𝐥𝘆" },
        { fid: 199, fnm: "𝗕𝗮𝐥𝐦𝐚𝗶𝐧" },
        { fid: 198, fnm: "𝐁𝗮𝘀𝐤𝗲𝘁𝐛𝗮𝐥𝐥 𝐒𝐡𝗼𝗲𝐬 for Real Games" },
        { fid: 197, fnm: "𝗕𝐞𝗿𝗹𝐮𝘁𝐢" },
        { fid: 196, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 195, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 194, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 193, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 192, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 191, fnm: "𝗗𝐨𝐥𝗰𝗲 &amp; 𝗚𝗮𝐛𝗯𝗮𝐧𝐚" },
        { fid: 190, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 189, fnm: "𝗙𝐞𝐫𝐫𝐚𝐠𝗮𝐦𝐨" },
        { fid: 188, fnm: "𝐆𝗶𝐯𝐞𝗻𝐜𝗵𝘆" },
        { fid: 187, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 186, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 185, fnm: "𝐊𝐞𝐧𝘇𝐨" },
        { fid: 184, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 183, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 182, fnm: "𝐌𝐜𝗤𝘂𝐞𝐞𝗻" },
        { fid: 181, fnm: "𝗠𝐨𝗻𝐜𝗹𝗲𝐫" },
        { fid: 180, fnm: "𝐎𝗳𝐟-𝗪𝗵𝐢𝘁𝗲" },
        { fid: 179, fnm: "𝐎𝐭𝐡𝗲𝗿 𝐁𝗿𝐚𝗻𝐝𝘀 𝐨𝗳" },
        { fid: 178, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 176, fnm: "𝐓𝐎𝐃'S" },
        { fid: 177, fnm: "𝐓𝗵𝐨𝗺 𝐁𝐫𝐨𝐰𝗻𝗲" },
        { fid: 175, fnm: "𝐔𝗚𝗚" },
        { fid: 174, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 173, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 171, fnm: "𝗬𝐒𝗟" },
        { fid: 172, fnm: "𝐘𝐞𝗲𝘇𝘆" },
        { fid: 170, fnm: "𝗭𝐞𝗴𝐧𝐚" },
      ],
    },

    {
      fid: 204,
      fnm: "Trendy Shoes",
      child: [
        { fid: 221, fnm: "𝐀𝗝" },
        { fid: 223, fnm: "𝐀𝗱𝗶𝗱𝗮𝐬 Originals" },
        { fid: 222, fnm: "𝐀𝐢𝗿 𝐅𝗼𝗿𝗰𝗲" },
        { fid: 220, fnm: "𝗔𝐬𝗶𝐜𝐬" },
        { fid: 219, fnm: "𝗖𝗼𝐧𝐯𝗲𝗿𝐬𝐞" },
        { fid: 218, fnm: "𝗗𝘂𝗻𝗸" },
        { fid: 217, fnm: "𝗘𝐜𝗰𝗼" },
        { fid: 216, fnm: "𝗚𝐆𝗖𝐂" },
        { fid: 214, fnm: "𝗠𝗟𝐁" },
        { fid: 215, fnm: "𝗠𝗶𝐡𝗮𝐫𝐚 𝐘𝗮𝐬𝘂𝐡𝗶𝐫𝗼" },
        { fid: 213, fnm: "𝗡𝗲𝐰 𝗕𝗮𝐥𝗮𝐧𝗰𝗲" },
        { fid: 211, fnm: "𝐏𝘂𝐦𝗮" },
        { fid: 210, fnm: "𝐑𝐢𝗰𝗸 𝗢𝐰𝐞𝐧𝐬" },
        { fid: 209, fnm: "𝗦𝗮𝐥𝗼𝐦𝐨𝐧" },
        { fid: 208, fnm: "𝗦𝐚𝗻𝗱𝗮𝗹𝐬 𝗮𝐧𝗱 𝐒𝗹𝗶𝗽𝐩𝐞𝐫𝐬" },
        { fid: 207, fnm: "𝐓𝗶𝐦𝗯𝐞𝐫𝐥𝐚𝗻𝗱" },
        { fid: 206, fnm: "𝐕𝗮𝐧𝘀" },
        { fid: 205, fnm: "𝐘𝐞𝗲𝘇𝘆" },
      ],
    },

    {
      fid: 349,
      fnm: "Belts",
      child: [
        { fid: 367, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 366, fnm: "𝗕𝐨𝘁𝐭𝐞𝐠𝐚 𝗩𝐞𝗻𝐞𝐭𝐚" },
        { fid: 365, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 364, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 363, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 362, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 361, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 360, fnm: "𝗙𝐞𝐫𝐫𝐚𝐠𝗮𝐦𝐨" },
        { fid: 359, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 358, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 356, fnm: "𝐋𝐕" },
        { fid: 357, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 355, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 354, fnm: "𝗠𝗼𝗻𝐭𝐛𝐥𝐚𝐧𝗰" },
        { fid: 353, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 352, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 351, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
        { fid: 350, fnm: "𝗬𝐒𝗟" },
      ],
    },

    {
      fid: 327,
      fnm: "Scarves",
      child: [
        { fid: 348, fnm: "𝐀𝐜𝐧𝗲 𝗦𝘁𝐮𝗱𝐢𝗼𝐬 Scarves" },
        { fid: 328, fnm: "𝐁𝗹𝐚𝗻𝐤𝐞𝐭" },
        { fid: 347, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆 Scarves" },
        { fid: 346, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲 Scarves" },
        { fid: 345, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥 Scarves" },
        { fid: 344, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥 Small Ribbons" },
        { fid: 343, fnm: "𝐃𝗶𝐨𝐫 Scarves" },
        { fid: 342, fnm: "𝐃𝗶𝐨𝐫 Small Ribbons" },
        { fid: 341, fnm: "𝐃𝗶𝐨𝐫 𝗧𝐢𝐞𝘀" },
        { fid: 340, fnm: "𝐅𝗲𝗻𝐝𝐢 Scarves" },
        { fid: 339, fnm: "𝐆𝗶***𝗵𝘆 Scarves" },
        { fid: 338, fnm: "𝐆𝘂𝗰𝗰𝐢 Scarves" },
        { fid: 337, fnm: "𝗛𝗲𝐫𝐦è𝐬 Scarves" },
        { fid: 336, fnm: "𝗛𝗲𝐫𝐦è𝐬 Small Ribbons" },
        { fid: 335, fnm: "𝗛𝗲𝐫𝐦è𝐬 𝗧𝐢𝐞𝘀" },
        { fid: 333, fnm: "𝐋𝐕 Scarves" },
        { fid: 332, fnm: "𝐋𝐕 Small Ribbons" },
        { fid: 331, fnm: "𝐋𝐕 𝗧𝐢𝐞𝘀" },
        { fid: 334, fnm: "𝐋𝐨𝗲𝐰𝐞 Scarves" },
        { fid: 330, fnm: "𝐌𝐜𝗤𝘂𝐞𝐞𝗻 Scarves" },
        { fid: 329, fnm: "𝐒𝐡𝐚𝘄𝐥" },
      ],
    },

    {
      fid: 310,
      fnm: "Eyewear",
      child: [
        { fid: 326, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 325, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 324, fnm: "𝗖𝗮𝐫𝐭𝗶𝗲𝐫" },
        { fid: 323, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 322, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 321, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 320, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 319, fnm: "𝐆𝗠" },
        { fid: 318, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 315, fnm: "𝐋𝐕" },
        { fid: 317, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 316, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 314, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 313, fnm: "𝗠𝗼𝗻𝐭𝐛𝐥𝐚𝐧𝗰" },
        { fid: 312, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 311, fnm: "𝐕𝐞𝗿𝘀𝗮𝐜𝗲" },
      ],
    },

    {
      fid: 294,
      fnm: "Hats",
      child: [
        { fid: 302, fnm: "𝐁𝐚𝐥𝗲𝗻𝐜𝗶𝐚𝐠𝗮" },
        { fid: 301, fnm: "𝗕𝐮𝐫𝗯𝗲𝗿𝐫𝘆" },
        { fid: 304, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 306, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 297, fnm: "𝗖𝐡𝗿𝗼𝗺𝐞 𝐇𝐞𝐚𝐫𝘁𝐬" },
        { fid: 307, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 298, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 308, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 309, fnm: "𝐋𝐕" },
        { fid: 299, fnm: "𝐋𝐨𝗲𝐰𝐞" },
        { fid: 300, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 295, fnm: "𝐏𝐢𝐥𝗹𝗼𝐰" },
        { fid: 305, fnm: "𝗣𝐫𝗮𝗱𝗮" },
        { fid: 296, fnm: "𝐕𝐚𝐥𝐞𝐧𝐭𝐢𝗻𝗼" },
        { fid: 303, fnm: "𝗬𝐒𝗟" },
      ],
    },

    {
      fid: 274,
      fnm: "Jewelry",
      child: [
        { fid: 293, fnm: "𝐁𝘂𝗹𝗴𝐚𝗿𝐢" },
        { fid: 292, fnm: "𝗖𝗮𝐫𝐭𝗶𝗲𝐫" },
        { fid: 291, fnm: "𝐂𝗲𝗹𝗶𝗻𝗲" },
        { fid: 290, fnm: "𝗖𝐡𝗮𝗻𝗲𝐥" },
        { fid: 289, fnm: "𝗖𝐡𝗿𝗼𝗺𝐞 𝐇𝐞𝐚𝐫𝘁𝐬" },
        { fid: 288, fnm: "𝐃𝗶𝐨𝐫" },
        { fid: 287, fnm: "𝐅𝗲𝗻𝐝𝐢" },
        { fid: 286, fnm: "𝗙𝗿𝐞𝗱" },
        { fid: 285, fnm: "𝐆𝗿𝐚𝗳𝗳" },
        { fid: 284, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 283, fnm: "𝗛𝗲𝐫𝐦è𝐬" },
        { fid: 282, fnm: "𝐋𝗼𝐮𝗶𝐬 𝗩𝐮𝐢𝐭𝐭𝗼𝐧" },
        { fid: 281, fnm: "𝗠𝐢𝐮 𝐌𝗶𝐮" },
        { fid: 280, fnm: "𝗡𝗶𝗰𝐡𝐞" },
        { fid: 279, fnm: "𝐐𝗲𝐞𝐥𝗶𝐧" },
        { fid: 278, fnm: "𝗧𝐢𝗳𝗳𝐚𝗻𝘆 &amp; Co." },
        { fid: 277, fnm: "𝗩𝗮𝗻 𝐂𝐥𝐞𝗲𝗳 &amp; 𝗔𝗿𝗽𝗲𝗹𝘀" },
        { fid: 276, fnm: "𝗩𝐢𝐯𝐢𝐞𝐧𝐧𝗲 𝐖𝗲𝐬𝐭𝘄𝗼𝗼𝗱" },
        { fid: 275, fnm: "𝗬𝐒𝗟" },
      ],
    },

    {
      fid: 224,
      fnm: "Watches",
      child: [
        { fid: 368, fnm: "𝗔𝐫𝗺𝐚𝗻𝐢" },
        { fid: 273, fnm: "𝗔𝘂𝐝𝐞𝐦𝐚𝗿𝐬 𝗣𝐢𝐠𝐮𝗲𝐭" },
        { fid: 268, fnm: "𝐁𝐕𝐋𝗚𝗔𝗥𝐈" },
        { fid: 272, fnm: "𝗕𝐞𝐥𝐥 &amp; 𝗥𝗼𝐬𝐬" },
        { fid: 271, fnm: "𝐁𝐥𝗮𝗻𝗰𝐩𝐚𝗶𝗻" },
        { fid: 270, fnm: "𝗕𝐫𝗲𝗴𝘂𝗲𝘁" },
        { fid: 269, fnm: "𝐁𝐫𝐞𝗶𝘁𝐥𝐢𝐧𝐠" },
        { fid: 266, fnm: "𝐂𝗔𝐒𝐈𝐎" },
        { fid: 265, fnm: "𝐂𝐇𝗔𝗡𝗘𝐋" },
        { fid: 264, fnm: "𝗖𝗵𝐨𝐩𝗮𝐫𝐝" },
        { fid: 263, fnm: "𝐂𝗼𝐫𝘂𝗺" },
        { fid: 260, fnm: "𝐅𝐑𝗔𝐍𝐂𝗞 𝐌𝗨𝐋𝐋𝐄𝗥" },
        { fid: 261, fnm: "𝗙𝐞𝐫𝐫𝐚𝐠𝗮𝐦𝐨" },
        { fid: 259, fnm: "𝐆𝗶𝗿𝗮𝐫𝐝 - 𝗣𝐞𝐫𝐫𝐞𝗴𝗮𝘂𝐱" },
        { fid: 258, fnm: "𝐆𝗹𝗮𝐬𝗵ü𝘁𝐭𝐞 𝗢𝐫𝐢𝗴𝗶𝐧𝗮𝐥" },
        { fid: 257, fnm: "𝗚𝐫𝐚𝐧𝗱 𝗦𝗲𝗶𝗸𝗼" },
        { fid: 256, fnm: "𝐆𝘂𝗰𝗰𝐢" },
        { fid: 254, fnm: "𝐇𝗨𝐁𝐋𝗢𝗧" },
        { fid: 253, fnm: "𝗜𝐖𝐂" },
        { fid: 252, fnm: "𝐉𝐀𝗘𝗚𝗘𝐑 - 𝗟𝐄𝐂𝗢𝗨𝐋𝐓𝐑𝐄" },
        { fid: 251, fnm: "𝗝𝐨𝐤𝗲𝗿" },
        { fid: 248, fnm: "𝐋𝗢𝐍𝐆𝗜𝐍𝗘𝐒" },
        { fid: 250, fnm: "𝐋𝐚𝐝𝗶𝐞𝘀" },
        { fid: 249, fnm: "𝐋𝐚𝗻𝗴𝗲 &amp; 𝗦ö𝗵𝐧𝐞" },
        { fid: 245, fnm: "𝐌𝗜𝗗𝐎" },
        { fid: 246, fnm: "𝐌𝗮𝐮𝐫𝐢𝗰𝐞 𝗟𝗮𝐜𝗿𝗼𝗶𝘅" },
        { fid: 244, fnm: "𝗠𝗼𝗻𝐭𝐛𝐥𝐚𝐧𝗰" },
        { fid: 243, fnm: "𝐍𝐎𝐌𝗢𝐒" },
        { fid: 242, fnm: "𝗢𝐌𝗘𝗚𝐀" },
        { fid: 241, fnm: "𝐎𝗥𝗜𝐒" },
        { fid: 239, fnm: "𝐏𝗔𝐑𝐌𝗜𝗚𝐈𝐀𝐍𝐈" },
        { fid: 238, fnm: "𝐏𝐀𝐓𝗘𝗞 𝐏𝐇𝐈𝗟𝐈𝐏𝐏𝐄" },
        { fid: 237, fnm: "𝐏𝐈𝐀𝗚𝗘𝐓" },
        { fid: 240, fnm: "𝐏𝐚𝗻𝐞𝗿𝐚𝗶" },
        { fid: 233, fnm: "𝐑𝐎𝐋𝐄𝐗" },
        { fid: 235, fnm: "𝗥𝐢𝐜𝐡𝐚𝐫𝐝 𝗠𝗶𝗹𝗹𝐞" },
        { fid: 234, fnm: "𝗥𝐨𝐠𝐞𝐫 𝗗𝐮𝗯𝐮𝐢𝘀" },
        { fid: 232, fnm: "𝗦𝐞𝐯𝐞𝗻𝗙𝐫𝗶𝐝𝗮𝘆" },
        { fid: 231, fnm: "𝗧𝗔𝗚 𝐇𝗲𝐮𝐞𝐫" },
        { fid: 230, fnm: "𝐓𝐈𝗦𝐒𝗢𝗧" },
        { fid: 229, fnm: "𝐓𝗨𝐃𝐎𝐑" },
        { fid: 228, fnm: "𝐔𝗟𝗬𝗦𝗦𝗘 𝗡𝐀𝐑𝐃𝗜𝗡" },
        { fid: 227, fnm: "𝐕𝐚𝐜𝗵𝗲𝗿𝗼𝐧 𝐂𝗼𝐧𝐬𝘁𝐚𝐧𝘁𝗶𝐧" },
        { fid: 226, fnm: "𝗩𝗮𝗻 𝐂𝐥𝐞𝗲𝗳 &amp; 𝗔𝗿𝗽𝗲𝗹𝘀" },
        { fid: 225, fnm: "𝐙𝗘𝗡𝐈𝐓𝐇" },
      ],
    },

    //           { fid: 372, fnm: "Uncategorized" },
    //    { fid: 479, fnm: "Blog" },
    //   { fid: 478, fnm: "Discuss" }
  ]
  let html = ""
  var timeOut
  var categoryMainDto = $("#categoryAllFindBox")
  list.forEach((v) => {
    if (v.child) {
      html += `<li fid="${v.fid}">${v.fnm}<i class="icon-caret-right"></i><div class="childBox childBox${v.fid}">`
      v.child.forEach((v2) => {
        html += `<div fid="${v2.fid}" class="categorySelectLi">${v2.fnm}</div>`
      })
      html += "</div></li>"
    } else {
      html += `<li fid="${v.fid}" class="categorySelectLi">${v.fnm}</li>`
    }
  })
  categoryMainDto.html(html)
  categoryMainDto.slideDown()
  categoryMainDto.mousemove(function () {
    clearTimeout(timeOut)
  })
  categoryMainDto.mouseout(function () {
    timeOut = setTimeout(() => {
      categoryMainDto.slideUp()
    }, 800)
  })

  $(".categorySelectLi").click(function () {
    var fname = $(this).text()
    var fid = $(this).attr("fid")
    $("#buttonCategory").attr("title", fname)
    $("#btnCategoryText").text(fname)
    $("#btnCategoryText").attr("fid", fid)
    $("#buttonCategory").addClass("categoryBtnClear")
    categoryMainDto.slideUp()
  })
}



function checkPrice() {
  $.alert(`The price of this item varies by category. For an exact quote, please place an order or contact our online customer service.<br/>
    <b>Super high-quality 1:1 copy replica</b><br/>
Handbag : $279 ~ $589USD<br/>
      Wallet : $120 ~ $298USD<br/>
      Watch : $539 ~ $659USD<br/>
      Clothing : $69 ~ $289USD<br/>
      Coat : $279 ~ $430USD<br/>
      Glasses : $89 ~ $129USD<br/>
      : $65 ~ $129USD<br/>
      Belt : $89 ~ $129USD<br/>
      Earrings : $49 ~ $65USD<br/>
      Scarf : $59 ~ $129USD<br/>
      Shoes : $110 ~ $270USD<br/>
      Luggage : $350 ~ $690USD<br/>
      Others : $49 ~ $199USD
`)
}

function isMobile() {
  return "ontouchstart" in document.documentElement
}

$(function () {
  var isLoading=false;
  if($("#listLoadMore").length){
  $(".pagination").hide()
  $("#listLoadMore").on("click", function () {
    let oldStr = location.href + ""
    const dataArr = oldStr.split("?")
    let curPage = $(this).attr("curPage") || 1
    // console.log(location.href, location,document.location)
    isLoading=true;
    

    if (location.pathname == "/") {
      dataArr[0] += "index/1"
    } else if (curPage == 1) {
      dataArr[0] += "/1"
    }
    oldStr = dataArr[0]
    let regex = new RegExp("/" + curPage + "$")
    curPage++
    let newUrl = oldStr.replace(regex, "/" + curPage)
    let This = $(this)
    if (dataArr[1]) newUrl += "?" + dataArr[1]
    This.hide()
    $("#loadingText,#endTextB").remove()
    $(".loadMoreBox").append('<div  id="loadingText" class="loading"> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span></div>')

    $.ajax({
      url: newUrl,
      type: "GET",
      success: function (json) {
        const data = JSON.parse(json)
        isLoading=false;
        if (data.message && (data.message.arrlist || data.message.threadlist)) {
          let html = ""
          let threadArr = []
          const threadlist =
            data.message.threadlist || data.message.arrlist.threadlist
          for (key in threadlist) {
            threadArr.push(threadlist[key])
          }
          threadArr.sort((a, b) => b.tid - a.tid)

          threadArr.forEach((v, index) => {
            let src = "src"
            if (index > 5) {
              src = "data-echo"
            }
            const imgList = getMiniImgUrl(getImgUrl(v.image_url))
            // console.log(333, imgList)
            const listImgArr = imgList.split(",")
            let img2 = '';
            listImgArr[1]=listImgArr[1]||listImgArr[0]
            if (!isMobile() && listImgArr[1] && listImgArr[1].indexOf('.mp4')<0) {
              img2 =`<img class="w-100 rounded img2" ${src}="${listImgArr[1]}">`
            }

            html += `<div class="threadlist col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2 threadLi" data-href="/read/${v.tid}" tid="${v.tid}">
              <div>
                <a href="/read/${v.tid}" class="thListImgLinkBox"><img src="/upload/loading.gif">
                  <img class="w-100 rounded img1" ${src}="${listImgArr[0]}">
                  ${img2}
                  </a>
                <div>
                  <div class="col-lg-12 subject"><a href="/read/${v.tid}" class="titleName"
                      aria-label="${v.subject}">${v.subject}</a>
                    <div class="proBox">`
            if (v.price > 0) {
              let price= v.price * 1.6;
              price=price.toFixed(2);
              html += `<span class="delPrice">$${
                price
              }</span><span class="priceBox priceItemNumBox"
                        price="${v.price}">$${v.price}</span>`
            } else {
              html += `<button class="btn btn-outline-secondary btn-sm checkPrice">Check Price</button>`
            }

            html += `</div><button type="button" class="btn btn-dark  btn-sm addCardBtn"><i class="icon-cart-plus"></i> Add to
                      cart</button>
                  </div>
                </div>
                </div>
            </div>
            `
          })

          $(".mainBody").append(html)
          echo.init({offset: 100,throttle: 250,unload: false});
          $("#loadingText").remove()
          This.attr("curPage", curPage)
          window.history.pushState({ path: oldStr }, "", newUrl)
          if (
            parseInt(data.message.num) / parseInt(data.message.pagesize) >
            data.message.page
          ) {
            This.show()
          } else {
            $("#loadingText").remove()
            $(".loadMoreBox").html(
              '<span id="endTextB">--- End ---</span>'
            )
          }
        } else {
          $("#loadingText").remove()
          $(".loadMoreBox").html('<span id="endTextB">--- End ---</span>')
        }
      },
      error: function (err) {
        isLoading=false;
        console.log(err)

      },
    })
  })

  $(window).on('scroll',function() {
    if(isLoading) return;
    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();
    var documentHeight = $(document).height();
    var scrollBottom = scrollTop + windowHeight;
    
    if (scrollBottom >= documentHeight - 100 && !isLoading) { // 100可以根据需要调整，表示距离底部的距离
       $("#listLoadMore").click()
    }
});

  }
})
