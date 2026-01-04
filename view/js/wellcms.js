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
        { fid: 21, fnm: "Alexander Wang Women's Bags" },
        { fid: 8, fnm: "Balenciaga Women's Bags" },
        { fid: 15, fnm: "Bottega Veneta Women's Bags" },
        { fid: 16, fnm: "Bulgari Women's Bags" },
        { fid: 11, fnm: "Burberry Women's Bags" },
        { fid: 10, fnm: "Celine Women's Bags" },
        { fid: 4, fnm: "Chanel Women's Bags" },
        { fid: 22, fnm: "Chloé Women's Bags" },
        { fid: 17, fnm: "Delvaux Women's Bags" },
        { fid: 5, fnm: "Dior Women's Bags" },
        { fid: 14, fnm: "Fendi Women's Bags" },
        { fid: 20, fnm: "Goyard Women's Bags" },
        { fid: 3, fnm: "Gucci Women's Bags" },
        { fid: 6, fnm: "Hermès Women's Bags" },
        { fid: 13, fnm: "Loewe Women's Bags" },
        { fid: 23, fnm: "Loro Piana Women's Bags" },
        { fid: 2, fnm: "Louis Vuitton Women's Bags" },
        { fid: 19, fnm: "MCM Women's Bags" },
        { fid: 12, fnm: "Miu Miu Women's Bags" },
        { fid: 9, fnm: "Prada Women's Bags" },
        { fid: 18, fnm: "Valentino Women's Bags" },
        { fid: 7, fnm: "YSL Women's Bags" },
      ],
    },

    {
      fid: 24,
      fnm: "Men's Bags",
      child: [
        { fid: 33, fnm: "Bally Men's Bags" },
        { fid: 31, fnm: "Bottega Veneta Men's Bags" },
        { fid: 30, fnm: "Burberry Men's Bags" },
        { fid: 27, fnm: "Dior Men's Bags" },
        { fid: 29, fnm: "Fendi Men's Bags" },
        { fid: 32, fnm: "Givenchy Men's Bags" },
        { fid: 26, fnm: "Gucci Men's Bags" },
        { fid: 25, fnm: "Louis Vuitton Men's Bags" },
        { fid: 35, fnm: "MCM Men's Bags" },
        { fid: 28, fnm: "Prada Men's Bags" },
        { fid: 34, fnm: "Thom Browne Men's Bags" },
      ],
    },

    {
      fid: 40,
      fnm: "Wallets",
      child: [
        { fid: 54, fnm: "Bally Wallets" },
        { fid: 47, fnm: "Burberry Wallets" },
        { fid: 49, fnm: "Celine Wallets" },
        { fid: 43, fnm: "Chanel Wallets" },
        { fid: 44, fnm: "Dior Wallets" },
        { fid: 50, fnm: "Fendi Wallets" },
        { fid: 51, fnm: "Givenchy Wallets" },
        { fid: 42, fnm: "Gucci Wallets" },
        { fid: 41, fnm: "Louis Vuitton Wallets" },
        { fid: 46, fnm: "Miu Miu Wallets" },
        { fid: 48, fnm: "Prada Wallets" },
        { fid: 52, fnm: "Versace Wallets" },
        { fid: 45, fnm: "YSL Wallets" },
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
        { fid: 78, fnm: "Acne Studios Women's Clothing" },
        { fid: 68, fnm: "Alexander Wang Women's Clothing" },
        { fid: 79, fnm: "Armani Women's Clothing" },
        { fid: 64, fnm: "Balenciaga Women's Clothing" },
        { fid: 81, fnm: "Balmain Women's Clothing" },
        { fid: 71, fnm: "Bottega Veneta Women's Clothing" },
        { fid: 61, fnm: "Burberry Women's Clothing" },
        { fid: 87, fnm: "Cashmere Coats, Furs" },
        { fid: 67, fnm: "Celine Women's Clothing" },
        { fid: 56, fnm: "Chanel Women's Clothing" },
        { fid: 73, fnm: "Chloé Women's Clothing" },
        { fid: 72, fnm: "Chrome Hearts Women's Clothing" },
        { fid: 58, fnm: "Dior Women's Clothing" },
        { fid: 84, fnm: "Dolce &amp; Gabbana Women's Clothing" },
        { fid: 66, fnm: "Fendi Women's Clothing" },
        { fid: 69, fnm: "Givenchy Women's Clothing" },
        { fid: 88, fnm: "Goose Down Jacket Women's" },
        { fid: 57, fnm: "Gucci Women's Clothing" },
        { fid: 59, fnm: "Hermès Women's Clothing" },
        { fid: 80, fnm: "Kenzo Women's Clothing" },
        { fid: 63, fnm: "Loewe Women's Clothing" },
        { fid: 370, fnm: "Louis Vuitton Women's Clothing" },
        { fid: 83, fnm: "MaxMara Women's Clothing" },
        { fid: 65, fnm: "Miu Miu Women's Clothing" },
        { fid: 74, fnm: "Moncler Women's Clothing" },
        { fid: 85, fnm: "Moschino Women's Clothing" },
        { fid: 82, fnm: "OffWhite Women's Clothing" },
        { fid: 90, fnm: "Other Brands of Women's Clothing" },
        { fid: 62, fnm: "Prada Women's Clothing" },
        { fid: 89, fnm: "Premium Women's Down Jackets" },
        { fid: 76, fnm: "The North Face Women's Clothing" },
        { fid: 75, fnm: "Thom Browne Women's Clothing" },
        { fid: 86, fnm: "Valentino Women's Clothing" },
        { fid: 70, fnm: "Versace Women's Clothing" },
        { fid: 60, fnm: "YSL Women's Clothing" },
        { fid: 77, fnm: "Zimmermann Women's Clothing" },
      ],
    },

    {
      fid: 91,
      fnm: "Men's Clothing",
      child: [
        { fid: 101, fnm: "Armani Men's Clothing" },
        { fid: 116, fnm: "Balenciaga Men's Clothing" },
        { fid: 109, fnm: "Bottega Veneta Men's Clothing" },
        { fid: 112, fnm: "Burberry Men's Clothing" },
        { fid: 94, fnm: "Canada Goose Men's Clothing" },
        { fid: 111, fnm: "Celine Men's Clothing" },
        { fid: 119, fnm: "Chanel Men's Clothing" },
        { fid: 102, fnm: "Chrome Hearts Men's Clothing" },
        { fid: 120, fnm: "Dior Men's Clothing" },
        { fid: 97, fnm: "Dolce &amp; Gabbana Men's Clothing" },
        { fid: 115, fnm: "Fendi Men's Clothing" },
        { fid: 104, fnm: "Givenchy Men's Clothing" },
        { fid: 121, fnm: "Gucci Men's Clothing" },
        { fid: 118, fnm: "Hermès Men's Clothing" },
        { fid: 99, fnm: "Kenzo Men's Clothing" },
        { fid: 114, fnm: "Loewe Men's Clothing" },
        { fid: 122, fnm: "Louis Vuitton Men's Clothing" },
        { fid: 105, fnm: "Mastermind Japan Men's Clothing" },
        { fid: 110, fnm: "Moncler Men's Clothing" },
        { fid: 98, fnm: "Moschino Men's Clothing" },
        { fid: 100, fnm: "Off-White Men's Clothing" },
        { fid: 95, fnm: "Other Brands of Men's Clothing" },
        { fid: 117, fnm: "Prada Men's Clothing" },
        { fid: 93, fnm: "Premium Men's Down Jackets" },
        { fid: 123, fnm: "Summer Shorts" },
        { fid: 107, fnm: "The North Face Men's Clothing" },
        { fid: 108, fnm: "Thom Browne Men's Clothing" },
        { fid: 96, fnm: "Valentino Men's Clothing" },
        { fid: 103, fnm: "Versace Men's Clothing" },
        { fid: 113, fnm: "YSL Men's Clothing" },
        { fid: 106, fnm: "Zegna Men's Clothing" },
      ],
    },

    {
      fid: 124,
      fnm: "Women's Shoes",
      child: [
        { fid: 168, fnm: "AIR JORDAN Women's Shoes" },
        { fid: 167, fnm: "Alexander Wang Women's Shoes" },
        { fid: 166, fnm: "Amiri Women's Shoes" },
        { fid: 165, fnm: "Balenciaga Women's Shoes" },
        { fid: 164, fnm: "Bally Women's Shoes" },
        { fid: 163, fnm: "Balmain Women's Shoes" },
        { fid: 162, fnm: "Bottega Veneta Women's Shoes" },
        { fid: 161, fnm: "Brunello Cucinelli Women's Shoes" },
        { fid: 160, fnm: "Burberry Women's Shoes" },
        { fid: 159, fnm: "Celine Women's Shoes" },
        { fid: 158, fnm: "Chanel Women's Shoes" },
        { fid: 157, fnm: "Chloé Women's Shoes" },
        { fid: 156, fnm: "Christian Louboutin Women's Shoes" },
        { fid: 155, fnm: "Dior Women's Shoes" },
        { fid: 154, fnm: "Dolce &amp; Gabbana Women's Shoes" },
        { fid: 153, fnm: "Fendi Women's Shoes" },
        { fid: 152, fnm: "Ferragamo Women's Shoes" },
        { fid: 151, fnm: "Givenchy Women's Shoes" },
        { fid: 150, fnm: "Golden Goose Women's Shoes" },
        { fid: 149, fnm: "Gucci Women's Shoes" },
        { fid: 147, fnm: "HOGAN Women's Shoes" },
        { fid: 148, fnm: "Hermès Women's Shoes" },
        { fid: 146, fnm: "JIL SANDER Women's Shoes" },
        { fid: 145, fnm: "Jimmy Choo Women's Shoes" },
        { fid: 144, fnm: "LANVIN Women's Shoes" },
        { fid: 143, fnm: "Loewe Women's Shoes" },
        { fid: 142, fnm: "Loro Piana Women's Shoes" },
        { fid: 141, fnm: "Louis Vuitton Women's Shoes" },
        { fid: 140, fnm: "Maison Margiela Women's Shoes" },
        { fid: 139, fnm: "Manolo Blahnik Women's Shoes" },
        { fid: 138, fnm: "Marni Women's Shoes" },
        { fid: 137, fnm: "McQueen Women's Shoes" },
        { fid: 136, fnm: "Miu Miu Women's Shoes" },
        { fid: 134, fnm: "Other Brands of Women's Shoes" },
        { fid: 133, fnm: "Prada Women's Shoes" },
        { fid: 132, fnm: "Rick Owens Women's Shoes" },
        { fid: 131, fnm: "Roger Vivier Women's Shoes" },
        { fid: 130, fnm: "Sergio Rossi Women's Shoes" },
        { fid: 129, fnm: "TODS Women's Shoes" },
        { fid: 128, fnm: "UGG Women's Shoes" },
        { fid: 127, fnm: "Valentino Women's Shoes" },
        { fid: 371, fnm: "Versace Women's Shoes" },
        { fid: 125, fnm: "YSL Women's Shoes" },
        { fid: 126, fnm: "Yeezy Women's Shoes" },
      ],
    },

    {
      fid: 169,
      fnm: "Men's Shoes",
      child: [
        { fid: 203, fnm: "AJ Men's Shoes" },
        { fid: 202, fnm: "Armani Men's Shoes" },
        { fid: 201, fnm: "Balenciaga Men's Shoes" },
        { fid: 200, fnm: "Bally Men's Shoes" },
        { fid: 199, fnm: "Balmain Men's Shoes" },
        { fid: 198, fnm: "Basketball Shoes for Real Games" },
        { fid: 197, fnm: "Berluti Men's Shoes" },
        { fid: 196, fnm: "Bottega Veneta Men's Shoes" },
        { fid: 195, fnm: "Burberry Men's Shoes" },
        { fid: 194, fnm: "Celine Men's Shoes" },
        { fid: 193, fnm: "Chanel Men's Shoes" },
        { fid: 192, fnm: "Dior Men's Shoes" },
        { fid: 191, fnm: "Dolce &amp; Gabbana Men's Shoes" },
        { fid: 190, fnm: "Fendi Men's Shoes" },
        { fid: 189, fnm: "Ferragamo Men's Shoes" },
        { fid: 188, fnm: "Givenchy Men's Shoes" },
        { fid: 187, fnm: "Gucci Men's Shoes" },
        { fid: 186, fnm: "Hermès Men's Shoes" },
        { fid: 185, fnm: "Kenzo Men's Shoes" },
        { fid: 184, fnm: "Loewe Men's Shoes" },
        { fid: 183, fnm: "Louis Vuitton Men's Shoes" },
        { fid: 182, fnm: "McQueen Men's Shoes" },
        { fid: 181, fnm: "Moncler Men's Shoes" },
        { fid: 180, fnm: "Off-White Men's Shoes" },
        { fid: 179, fnm: "Other Brands of Men's Shoes" },
        { fid: 178, fnm: "Prada Men's Shoes" },
        { fid: 176, fnm: "TOD'S Men's Shoes" },
        { fid: 177, fnm: "Thom Browne Men's Shoes" },
        { fid: 175, fnm: "UGG Men's Shoes" },
        { fid: 174, fnm: "Valentino Men's Shoes" },
        { fid: 173, fnm: "Versace Men's Shoes" },
        { fid: 171, fnm: "YSL Men's Shoes" },
        { fid: 172, fnm: "Yeezy Men's Shoes" },
        { fid: 170, fnm: "Zegna Men's Shoes" },
      ],
    },

    {
      fid: 204,
      fnm: "Trendy Shoes",
      child: [
        { fid: 221, fnm: "AJ Series" },
        { fid: 223, fnm: "Adidas Originals Series" },
        { fid: 222, fnm: "Air Force Series" },
        { fid: 220, fnm: "Asics Series" },
        { fid: 219, fnm: "Converse Series" },
        { fid: 218, fnm: "Dunk Series" },
        { fid: 217, fnm: "Ecco Series" },
        { fid: 216, fnm: "GGCC Series" },
        { fid: 214, fnm: "MLB Series" },
        { fid: 215, fnm: "Mihara Yasuhiro Series" },
        { fid: 213, fnm: "New Balance Series" },
        { fid: 211, fnm: "Puma Series" },
        { fid: 210, fnm: "Rick Owens Series" },
        { fid: 209, fnm: "Salomon Series" },
        { fid: 208, fnm: "Sandals and Slippers Series" },
        { fid: 207, fnm: "Timberland Series" },
        { fid: 206, fnm: "Vans Series" },
        { fid: 205, fnm: "Yeezy Series" },
      ],
    },

    {
      fid: 349,
      fnm: "Belts",
      child: [
        { fid: 367, fnm: "Balenciaga Belts" },
        { fid: 366, fnm: "Bottega Veneta Belts" },
        { fid: 365, fnm: "Burberry Belts" },
        { fid: 364, fnm: "Celine Belts" },
        { fid: 363, fnm: "Chanel Belts" },
        { fid: 362, fnm: "Dior Belts" },
        { fid: 361, fnm: "Fendi Belts" },
        { fid: 360, fnm: "Ferragamo Belts" },
        { fid: 359, fnm: "Gucci Belts" },
        { fid: 358, fnm: "Hermès Belts" },
        { fid: 356, fnm: "LV Belts" },
        { fid: 357, fnm: "Loewe Belts" },
        { fid: 355, fnm: "Miu Miu Belts" },
        { fid: 354, fnm: "Montblanc Belts" },
        { fid: 353, fnm: "Prada Belts" },
        { fid: 352, fnm: "Valentino Belts" },
        { fid: 351, fnm: "Versace Belts" },
        { fid: 350, fnm: "YSL Belts" },
      ],
    },

    {
      fid: 327,
      fnm: "Scarves",
      child: [
        { fid: 348, fnm: "Acne Studios Scarves" },
        { fid: 328, fnm: "Blanket Series" },
        { fid: 347, fnm: "Burberry Scarves" },
        { fid: 346, fnm: "Celine Scarves" },
        { fid: 345, fnm: "Chanel Scarves" },
        { fid: 344, fnm: "Chanel Small Ribbons" },
        { fid: 343, fnm: "Dior Scarves" },
        { fid: 342, fnm: "Dior Small Ribbons" },
        { fid: 341, fnm: "Dior Ties" },
        { fid: 340, fnm: "Fendi Scarves" },
        { fid: 339, fnm: "Givenchy Scarves" },
        { fid: 338, fnm: "Gucci Scarves" },
        { fid: 337, fnm: "Hermès Scarves" },
        { fid: 336, fnm: "Hermès Small Ribbons" },
        { fid: 335, fnm: "Hermès Ties" },
        { fid: 333, fnm: "LV Scarves" },
        { fid: 332, fnm: "LV Small Ribbons" },
        { fid: 331, fnm: "LV Ties" },
        { fid: 334, fnm: "Loewe Scarves" },
        { fid: 330, fnm: "McQueen Scarves" },
        { fid: 329, fnm: "Shawl Series" },
      ],
    },

    {
      fid: 310,
      fnm: "Eyewear",
      child: [
        { fid: 326, fnm: "Balenciaga Eyewear" },
        { fid: 325, fnm: "Burberry Eyewear" },
        { fid: 324, fnm: "Cartier Eyewear" },
        { fid: 323, fnm: "Celine Eyewear" },
        { fid: 322, fnm: "Chanel Eyewear" },
        { fid: 321, fnm: "Dior Eyewear" },
        { fid: 320, fnm: "Fendi Eyewear" },
        { fid: 319, fnm: "GM Eyewear" },
        { fid: 318, fnm: "Gucci Eyewear" },
        { fid: 315, fnm: "LV Eyewear" },
        { fid: 317, fnm: "Loewe Eyewear" },
        { fid: 316, fnm: "Louis Vuitton Eyewear" },
        { fid: 314, fnm: "Miu Miu Eyewear" },
        { fid: 313, fnm: "Montblanc Eyewear" },
        { fid: 312, fnm: "Prada Eyewear" },
        { fid: 311, fnm: "Versace Eyewear" },
      ],
    },

    {
      fid: 294,
      fnm: "Hats",
      child: [
        { fid: 302, fnm: "Balenciaga Hats" },
        { fid: 301, fnm: "Burberry Hats" },
        { fid: 304, fnm: "Celine Hats" },
        { fid: 306, fnm: "Chanel Hats" },
        { fid: 297, fnm: "Chrome Hearts Hats" },
        { fid: 307, fnm: "Dior Hats" },
        { fid: 298, fnm: "Fendi Hats" },
        { fid: 308, fnm: "Gucci Hats" },
        { fid: 309, fnm: "LV Hats" },
        { fid: 299, fnm: "Loewe Hats" },
        { fid: 300, fnm: "Miu Miu Hats" },
        { fid: 295, fnm: "Pillow Series" },
        { fid: 305, fnm: "Prada Hats" },
        { fid: 296, fnm: "Valentino Hats" },
        { fid: 303, fnm: "YSL Hats" },
      ],
    },

    {
      fid: 274,
      fnm: "Jewelry",
      child: [
        { fid: 293, fnm: "Bulgari Jewelry" },
        { fid: 292, fnm: "Cartier Jewelry" },
        { fid: 291, fnm: "Celine Jewelry" },
        { fid: 290, fnm: "Chanel Jewelry" },
        { fid: 289, fnm: "Chrome Hearts Jewelry" },
        { fid: 288, fnm: "Dior Jewelry" },
        { fid: 287, fnm: "Fendi Jewelry" },
        { fid: 286, fnm: "Fred Jewelry" },
        { fid: 285, fnm: "Graff Jewelry" },
        { fid: 284, fnm: "Gucci Jewelry" },
        { fid: 283, fnm: "Hermès Jewelry" },
        { fid: 282, fnm: "Louis Vuitton Jewelry" },
        { fid: 281, fnm: "Miu Miu Jewelry" },
        { fid: 280, fnm: "Niche Series Jewelry" },
        { fid: 279, fnm: "Qeelin Jewelry" },
        { fid: 278, fnm: "Tiffany &amp; Co. Jewelry" },
        { fid: 277, fnm: "Van Cleef &amp; Arpels Jewelry" },
        { fid: 276, fnm: "Vivienne Westwood Jewelry" },
        { fid: 275, fnm: "YSL Jewelry" },
      ],
    },

    {
      fid: 224,
      fnm: "Watches",
      child: [
        { fid: 368, fnm: "Armani Watches" },
        { fid: 273, fnm: "Audemars Piguet Watches" },
        { fid: 268, fnm: "BVLGARI Watches" },
        { fid: 272, fnm: "Bell &amp; Ross Watches" },
        { fid: 271, fnm: "Blancpain Watches" },
        { fid: 270, fnm: "Breguet Watches" },
        { fid: 269, fnm: "Breitling Watches" },
        { fid: 266, fnm: "CASIO Watches" },
        { fid: 265, fnm: "CHANEL Watches" },
        { fid: 264, fnm: "Chopard Watches" },
        { fid: 263, fnm: "Corum Watches" },
        { fid: 260, fnm: "FRANCK MULLER Watches" },
        { fid: 261, fnm: "Ferragamo Watches" },
        { fid: 259, fnm: "Girard - Perregaux Watches" },
        { fid: 258, fnm: "Glashütte Original Watches" },
        { fid: 257, fnm: "Grand Seiko Watches" },
        { fid: 256, fnm: "Gucci Watches" },
        { fid: 254, fnm: "HUBLOT Watches" },
        { fid: 253, fnm: "IWC Watches" },
        { fid: 252, fnm: "JAEGER - LECOULTRE Watches" },
        { fid: 251, fnm: "Joker Watches" },
        { fid: 248, fnm: "LONGINES Watches" },
        { fid: 250, fnm: "Ladies Watches" },
        { fid: 249, fnm: "Lange &amp; Söhne Watches" },
        { fid: 245, fnm: "MIDO Watches" },
        { fid: 246, fnm: "Maurice Lacroix Watches" },
        { fid: 244, fnm: "Montblanc Watches" },
        { fid: 243, fnm: "NOMOS Watches" },
        { fid: 242, fnm: "OMEGA Watches" },
        { fid: 241, fnm: "ORIS Watches" },
        { fid: 239, fnm: "PARMIGIANI Watches" },
        { fid: 238, fnm: "PATEK PHILIPPE Watches" },
        { fid: 237, fnm: "PIAGET Watches" },
        { fid: 240, fnm: "Panerai Watches" },
        { fid: 233, fnm: "ROLEX Watches" },
        { fid: 235, fnm: "Richard Mille Watches" },
        { fid: 234, fnm: "Roger Dubuis Watches" },
        { fid: 232, fnm: "SevenFriday Watches" },
        { fid: 231, fnm: "TAG Heuer Watches" },
        { fid: 230, fnm: "TISSOT Watches" },
        { fid: 229, fnm: "TUDOR Watches" },
        { fid: 228, fnm: "ULYSSE NARDIN Watches" },
        { fid: 227, fnm: "Vacheron Constantin Watches" },
        { fid: 226, fnm: "Van Cleef &amp; Arpels Watches" },
        { fid: 225, fnm: "ZENITH Watches" },
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
      Jewelry : $65 ~ $129USD<br/>
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
