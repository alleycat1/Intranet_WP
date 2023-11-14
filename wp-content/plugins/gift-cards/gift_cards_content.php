<style type="text/css">
     .paid_out:hover {
          color: #FF00FF;
     }
     .paid_out {
          cursor: pointer;
     }
     .my_td{
          padding-top:5px;
          padding-bottom:5px;
          border:0px;
     }
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<?php
require_once(ABSPATH . 'wp-admin/includes/file.php');

$uploads = wp_upload_dir();
$upload_url = $uploads['baseurl'];
?>

<style>
     /*jssor slider loading skin spin css*/
     .jssorl-009-spin img {
          animation-name: jssorl-009-spin;
          animation-duration: 1.6s;
          animation-iteration-count: infinite;
          animation-timing-function: linear;
     }

     @keyframes jssorl-009-spin {
          from { transform: rotate(0deg); }
          to { transform: rotate(360deg); }
     }

     /*jssor slider arrow skin 093 css*/
     .jssora093 {display:block;position:absolute;cursor:pointer;}
     .jssora093 .c {fill:none;stroke:#fff;stroke-width:400;stroke-miterlimit:10;}
     .jssora093 .a {fill:none;stroke:#fff;stroke-width:400;stroke-miterlimit:10;}
     .jssora093:hover {opacity:.8;}
     .jssora093.jssora093dn {opacity:.6;}
     .jssora093.jssora093ds {opacity:.3;pointer-events:none;}

     /*jssor slider thumbnail skin 101 css*/
     .jssort101 .p {position: absolute;top:0;left:0;box-sizing:border-box;background:#000;}
     .jssort101 .p .cv {position:relative;top:0;left:0;width:100%;height:100%;border:2px solid #aaaaaa;box-sizing:border-box;z-index:1;}
     .jssort101 .a {fill:none;stroke:#fff;stroke-width:400;stroke-miterlimit:10;visibility:hidden;}
     .jssort101 .p:hover .cv, .jssort101 .p.pdn .cv {border:none;border-color:transparent;}
     .jssort101 .p:hover{padding:2px;}
     .jssort101 .p:hover .cv {background-color:rgba(0,0,0,6);opacity:.35;}
     .jssort101 .p:hover.pdn{padding:0;}
     .jssort101 .p:hover.pdn .cv {border:2px solid #fff;background:none;opacity:.35;}
     .jssort101 .pav .cv {border-color:#fff;opacity:.35;}
     .jssort101 .pav .a, .jssort101 .p:hover .a {visibility:visible;}
     .jssort101 .t {position:absolute;top:0;left:0;width:100%;height:100%;border:none;opacity:.6;}
     .jssort101 .pav .t, .jssort101 .p:hover .t{opacity:1;}
</style>

<span style="border:0px; width:1024px; text-align:center"><H1>GIFT CARDS</H1></span>

<div style="position:relative;margin:0 auto;top:0px;left:0px;width:1024px;height:598px;overflow:hidden; background-image:url('<?php echo $upload_url;?>/gift_cards/frame.png');border:0px;background-repeat:no-reapeat;background-size:cover;">
     <div id="jssor_1" style="position:relative;margin:0 auto;top:30px;left:-1px;width:860px;height:480px;overflow:hidden;visibility:hidden;background-color:#ffffff;border-radius:4px;border:0px solid #ffffff;">
          <!-- Loading Screen -->
          <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
               <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="<?php echo $upload_url;?>/gift_cards/spin.svg" />
          </div>
          <div data-u="slides" onclick="alert('asdf')" style="cursor:pointer;position:relative;top:0px;left:140px;width:720px;height:480px;overflow:hidden;">
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift1.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift1.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift2.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift2.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift3.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift3.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift4.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift4.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift5.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift5.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift6.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift6.png" />
               </div>
               <div>
                    <img data-u="image" src="<?php echo $upload_url;?>/gift_cards/gift7.png" />
                    <img data-u="thumb" src="<?php echo $upload_url;?>/gift_cards/gift7.png" />
               </div>
          </div>
          <!-- Thumbnail Navigator -->
          <div data-u="thumbnavigator" class="jssort101" style="position:absolute;left:0px;top:0px;width:140px;height:480px;background-color:#333;background-image:url('<?php echo $upload_url;?>/gift_cards/snow.png');" data-autocenter="2" data-scale-left="0.75">
               <div data-u="slides">
                    <div data-u="prototype" class="p" style="width:99px;height:66px;">
                    <div data-u="thumbnailtemplate" class="t"></div>
                    <svg viewbox="0 0 16000 16000" class="cv">
                         <circle class="a" cx="8000" cy="8000" r="3238.1"></circle>
                         <line class="a" x1="6190.5" y1="8000" x2="9809.5" y2="8000"></line>
                         <line class="a" x1="8000" y1="9809.5" x2="8000" y2="6190.5"></line>
                    </svg>
                    </div>
               </div>
          </div>
          <!-- Arrow Navigator -->
          <div data-u="arrowleft" class="jssora093" style="width:50px;height:50px;top:0px;left:170px;" data-autocenter="2">
               <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="7777.8,6080 5857.8,8000 7777.8,9920 "></polyline>
                    <line class="a" x1="10142.2" y1="8000" x2="5857.8" y2="8000"></line>
               </svg>
          </div>
          <div data-u="arrowright" class="jssora093" style="width:50px;height:50px;top:0px;right:30px;" data-autocenter="2">
               <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="8222.2,6080 10142.2,8000 8222.2,9920 "></polyline>
                    <line class="a" x1="5857.8" y1="8000" x2="10142.2" y2="8000"></line>
               </svg>
          </div>
     </div>
</div>
<script type="text/javascript">
!function(i, h, m, e, d, k, f) {
    new (function() {}
    );
    var c = {
        q: m.PI,
        l: m.max,
        j: m.min,
        H: m.ceil,
        G: m.floor,
        P: m.abs,
        eb: m.sin,
        Gb: m.cos,
        qe: m.tan,
        sh: m.atan,
        zh: m.atan2,
        Hb: m.sqrt,
        z: m.pow,
        Tc: m.random,
        _Round: m.round,
        Y: function(d, b) {
            var a = c.z(10, b || 0);
            return c._Round(d * a) / a
        }
    };
    function r(a) {
        return function(b) {
            return c._Round(b * a) / a
        }
    }
    var g = i._Jease = {
        _Swing: function(a) {
            return -c.Gb(a * c.q) / 2 + .5
        },
        _Linear: function(a) {
            return a
        },
        _InQuad: function(a) {
            return a * a
        },
        _OutQuad: function(a) {
            return -a * (a - 2)
        },
        _InOutQuad: function(a) {
            return (a *= 2) < 1 ? 1 / 2 * a * a : -1 / 2 * (--a * (a - 2) - 1)
        },
        _InCubic: function(a) {
            return a * a * a
        },
        _OutCubic: function(a) {
            return (a -= 1) * a * a + 1
        },
        _InOutCubic: function(a) {
            return (a *= 2) < 1 ? 1 / 2 * a * a * a : 1 / 2 * ((a -= 2) * a * a + 2)
        },
        _InQuart: function(a) {
            return a * a * a * a
        },
        _OutQuart: function(a) {
            return -((a -= 1) * a * a * a - 1)
        },
        _InOutQuart: function(a) {
            return (a *= 2) < 1 ? 1 / 2 * a * a * a * a : -1 / 2 * ((a -= 2) * a * a * a - 2)
        },
        _InQuint: function(a) {
            return a * a * a * a * a
        },
        _OutQuint: function(a) {
            return (a -= 1) * a * a * a * a + 1
        },
        _InOutQuint: function(a) {
            return (a *= 2) < 1 ? 1 / 2 * a * a * a * a * a : 1 / 2 * ((a -= 2) * a * a * a * a + 2)
        },
        _InSine: function(a) {
            return 1 - c.Gb(c.q / 2 * a)
        },
        _OutSine: function(a) {
            return c.eb(c.q / 2 * a)
        },
        _InOutSine: function(a) {
            return -1 / 2 * (c.Gb(c.q * a) - 1)
        },
        _InExpo: function(a) {
            return a == 0 ? 0 : c.z(2, 10 * (a - 1))
        },
        _OutExpo: function(a) {
            return a == 1 ? 1 : -c.z(2, -10 * a) + 1
        },
        _InOutExpo: function(a) {
            return a == 0 || a == 1 ? a : (a *= 2) < 1 ? 1 / 2 * c.z(2, 10 * (a - 1)) : 1 / 2 * (-c.z(2, -10 * --a) + 2)
        },
        _InCirc: function(a) {
            return -(c.Hb(1 - a * a) - 1)
        },
        _OutCirc: function(a) {
            return c.Hb(1 - (a -= 1) * a)
        },
        _InOutCirc: function(a) {
            return (a *= 2) < 1 ? -1 / 2 * (c.Hb(1 - a * a) - 1) : 1 / 2 * (c.Hb(1 - (a -= 2) * a) + 1)
        },
        _InElastic: function(a) {
            if (!a || a == 1)
                return a;
            var b = .3
              , d = .075;
            return -(c.z(2, 10 * (a -= 1)) * c.eb((a - d) * 2 * c.q / b))
        },
        _OutElastic: function(a) {
            if (!a || a == 1)
                return a;
            var b = .3
              , d = .075;
            return c.z(2, -10 * a) * c.eb((a - d) * 2 * c.q / b) + 1
        },
        _InOutElastic: function(a) {
            if (!a || a == 1)
                return a;
            var b = .45
              , d = .1125;
            return (a *= 2) < 1 ? -.5 * c.z(2, 10 * (a -= 1)) * c.eb((a - d) * 2 * c.q / b) : c.z(2, -10 * (a -= 1)) * c.eb((a - d) * 2 * c.q / b) * .5 + 1
        },
        _InBack: function(a) {
            var b = 1.70158;
            return a * a * ((b + 1) * a - b)
        },
        _OutBack: function(a) {
            var b = 1.70158;
            return (a -= 1) * a * ((b + 1) * a + b) + 1
        },
        _InOutBack: function(a) {
            var b = 1.70158;
            return (a *= 2) < 1 ? 1 / 2 * a * a * (((b *= 1.525) + 1) * a - b) : 1 / 2 * ((a -= 2) * a * (((b *= 1.525) + 1) * a + b) + 2)
        },
        _InBounce: function(a) {
            return 1 - g._OutBounce(1 - a)
        },
        _OutBounce: function(a) {
            return a < 1 / 2.75 ? 7.5625 * a * a : a < 2 / 2.75 ? 7.5625 * (a -= 1.5 / 2.75) * a + .75 : a < 2.5 / 2.75 ? 7.5625 * (a -= 2.25 / 2.75) * a + .9375 : 7.5625 * (a -= 2.625 / 2.75) * a + .984375
        },
        _InOutBounce: function(a) {
            return a < 1 / 2 ? g._InBounce(a * 2) * .5 : g._OutBounce(a * 2 - 1) * .5 + .5
        },
        _GoBack: function(a) {
            return 1 - c.P(2 - 1)
        },
        _InWave: function(a) {
            return 1 - c.Gb(a * c.q * 2)
        },
        _OutWave: function(a) {
            return c.eb(a * c.q * 2)
        },
        _OutJump: function(a) {
            return 1 - ((a *= 2) < 1 ? (a = 1 - a) * a * a : (a -= 1) * a * a)
        },
        _InJump: function(a) {
            return (a *= 2) < 1 ? a * a * a : (a = 2 - a) * a * a
        },
        _Early: c.H,
        _Late: c.G,
        _Mid: c._Round,
        _Mid2: r(2),
        _Mid3: r(3),
        _Mid4: r(4),
        _Mid5: r(5),
        _Mid6: r(6)
    };
    function v(k, l, p) {
        var d = this
          , f = [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, k || 0, l || 0, p || 0, 1]
          , j = c.eb
          , i = c.Gb
          , n = c.qe;
        function h(a) {
            return a * c.q / 180
        }
        function o(b, c, f, g, i, l, n, o, q, t, u, w, y, A, C, F, a, d, e, h, j, k, m, p, r, s, v, x, z, B, D, E) {
            return [b * a + c * j + f * r + g * z, b * d + c * k + f * s + g * B, b * e + c * m + f * v + g * D, b * h + c * p + f * x + g * E, i * a + l * j + n * r + o * z, i * d + l * k + n * s + o * B, i * e + l * m + n * v + o * D, i * h + l * p + n * x + o * E, q * a + t * j + u * r + w * z, q * d + t * k + u * s + w * B, q * e + t * m + u * v + w * D, q * h + t * p + u * x + w * E, y * a + A * j + C * r + F * z, y * d + A * k + C * s + F * B, y * e + A * m + C * v + F * D, y * h + A * p + C * x + F * E]
        }
        function g(b, a) {
            return o.apply(e, (a || f).concat(b))
        }
        d._Scale = function(a, b, c) {
            if (a != 1 || b != 1 || c != 1)
                f = g([a, 0, 0, 0, 0, b, 0, 0, 0, 0, c, 0, 0, 0, 0, 1])
        }
        ;
        d._Move = function(a, b, c) {
            f[12] += a || 0;
            f[13] += b || 0;
            f[14] += c || 0
        }
        ;
        d._RotateX = function(b) {
            if (b) {
                a = h(b);
                var c = i(a)
                  , d = j(a);
                f = g([1, 0, 0, 0, 0, c, d, 0, 0, -d, c, 0, 0, 0, 0, 1])
            }
        }
        ;
        d._RotateY = function(b) {
            if (b) {
                a = h(b);
                var c = i(a)
                  , d = j(a);
                f = g([c, 0, -d, 0, 0, 1, 0, 0, d, 0, c, 0, 0, 0, 0, 1])
            }
        }
        ;
        d.gf = function(a) {
            d.ef(h(a))
        }
        ;
        d.ef = function(a) {
            if (a) {
                var b = i(a)
                  , c = j(a);
                f = g([b, c, 0, 0, -c, b, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1])
            }
        }
        ;
        d.kf = function(a, b) {
            if (a || b) {
                k = h(a);
                l = h(b);
                f = g([1, n(l), 0, 0, n(k), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1])
            }
        }
        ;
        d.yf = function() {
            return "matrix3d(" + f.join(",") + ")"
        }
        ;
        d.Cf = function() {
            return "matrix(" + [f[0], f[1], f[4], f[5], f[12], f[13]].join(",") + ")"
        }
    }
    var b = i._Jssor = new function() {
        var a = this, Eb = /\S+/g, R = 1, mb = 2, qb = 3, pb = 4, tb = 5, T, t = 0, n = 0, I = 0, M = navigator, Ab = M.appName, o = M.userAgent, r = parseFloat;
        function w(c, a, b) {
            return c.indexOf(a, b)
        }
        function Qb() {
            if (!T) {
                T = {
                    wd: "ontouchstart"in i || "createTouch"in h
                };
                var a;
                if (M.pointerEnabled || (a = M.msPointerEnabled))
                    T.Pe = a ? "msTouchAction" : "touchAction"
            }
            return T
        }
        function y(g) {
            if (!t) {
                t = -1;
                if (Ab == "Microsoft Internet Explorer" && !!i.attachEvent && !!i.ActiveXObject) {
                    var e = w(o, "MSIE");
                    t = R;
                    n = r(o.substring(e + 5, w(o, ";", e)))
                } else if (Ab == "Netscape" && !!i.addEventListener) {
                    var d = w(o, "Firefox")
                      , b = w(o, "Safari")
                      , f = w(o, "Chrome")
                      , c = w(o, "AppleWebKit");
                    if (d >= 0) {
                        t = mb;
                        n = r(o.substring(d + 8))
                    } else if (b >= 0) {
                        var h = o.substring(0, b).lastIndexOf("/");
                        t = f >= 0 ? pb : qb;
                        n = r(o.substring(h + 1, b))
                    } else {
                        var a = /Trident\/.*rv:([0-9]{1,}[\.0-9]{0,})/i.exec(o);
                        if (a) {
                            t = R;
                            n = r(a[1])
                        }
                    }
                    if (c >= 0)
                        I = r(o.substring(c + 12))
                } else {
                    var a = /(opera)(?:.*version|)[ \/]([\w.]+)/i.exec(o);
                    if (a) {
                        t = tb;
                        n = r(a[2])
                    }
                }
            }
            return g == t
        }
        function F() {
            return y(R)
        }
        function ob() {
            return y(qb)
        }
        function sb() {
            return y(tb)
        }
        function B() {
            y();
            return I > 537 || n > 42 || t == R && n >= 11
        }
        function U(a) {
            var b;
            return function(d) {
                if (!b) {
                    var c = a.substr(0, 1).toUpperCase() + a.substr(1);
                    b = j(["", "WebKit", "ms", "Moz", "O", "webkit"], function(b) {
                        var e = b + (b ? c : a);
                        return d.style[e] != f && e
                    }) || a
                }
                return b
            }
        }
        var O = l("transform", 8);
        function J(a) {
            return a
        }
        function P(a) {
            return i.SVGElement && a instanceof i.SVGElement
        }
        function Ob(a) {
            return {}.toString.call(a)
        }
        var S = Array.isArray || function(a) {
            return N(a) == "array"
        }
          , wb = {};
        j(["Boolean", "Number", "String", "Function", "Array", "Date", "RegExp", "Object"], function(a) {
            wb["[object " + a + "]"] = a.toLowerCase()
        });
        function j(b, d) {
            var a, c;
            if (S(b)) {
                for (a = 0; a < m(b); a++)
                    if (c = d(b[a], a, b))
                        return c
            } else
                for (a in b)
                    if (c = d(b[a], a, b))
                        return c
        }
        function N(a) {
            return a == e ? String(a) : wb[Ob(a)] || "object"
        }
        function Z(a) {
            for (var b in a)
                return d
        }
        function D(a) {
            try {
                return N(a) == "object" && !a.nodeType && a != a.window && (!a.constructor || {}.hasOwnProperty.call(a.constructor.prototype, "isPrototypeOf"))
            } catch (b) {}
        }
        function Db(a, b) {
            return {
                x: a,
                y: b
            }
        }
        function Rb(b, a) {
            setTimeout(b, a || 0)
        }
        function q(a, b) {
            return a === f ? b : a
        }
        a.Ad = Qb;
        a.Ue = F;
        a.Ve = ob;
        a.th = B;
        a.Zc = function() {
            return n
        }
        ;
        a._Delay = Rb;
        a.mc = q;
        a.V = function(a, b) {
            b.call(a);
            return x({}, a)
        }
        ;
        function bb(a) {
            a.constructor === bb.caller && a.F && a.F.apply(a, bb.caller.arguments)
        }
        a.F = bb;
        a._GetElement = function(b) {
            if (a.jh(b))
                b = h.getElementById(b);
            return b
        }
        ;
        a.oc = function(c) {
            var b = [];
            j(c, function(d) {
                var c = a._GetElement(d);
                c && b.push(c)
            });
            return b
        }
        ;
        function u(a) {
            return a || i.event
        }
        a.kh = u;
        a._EvtSrc = function(c) {
            c = u(c);
            var b = c.target || c.srcElement || h;
            if (b.nodeType == 3)
                b = a.Mb(b);
            return b
        }
        ;
        a.Ee = function(a) {
            a = u(a);
            return a.relatedTarget || a.toElement
        }
        ;
        a.Be = function(a) {
            a = u(a);
            return a.which || ([0, 1, 3, 0, 2])[a.button] || a.charCode || a.keyCode
        }
        ;
        a.bd = function(a) {
            a = u(a);
            return {
                x: a.clientX || 0,
                y: a.clientY || 0
            }
        }
        ;
        a.Le = function(a, b) {
            return Db(a.x - b.x, a.y - b.y)
        }
        ;
        a.ph = function(a, b) {
            return a.x >= b.x && a.x <= b.t && a.y >= b.y && a.y <= b.m
        }
        ;
        a.Te = function(d, f) {
            var c = b.mh(f)
              , e = b.bd(d);
            return a.ph(e, c)
        }
        ;
        a.Vb = function(b) {
            return P(a.Mb(b))
        }
        ;
        function A(c, d, a) {
            if (a !== f)
                c.style[d] = a == f ? "" : a;
            else {
                var b = c.currentStyle || c.style;
                a = b[d];
                if (a == "" && i.getComputedStyle) {
                    b = c.ownerDocument.defaultView.getComputedStyle(c, e);
                    b && (a = b.getPropertyValue(d) || b[d])
                }
                return a
            }
        }
        function fb(b, c, a, d) {
            if (a === f) {
                a = r(A(b, c));
                isNaN(a) && (a = e);
                return a
            }
            d && a != e && (a += d);
            A(b, c, a)
        }
        function l(g, a, b, d) {
            var c;
            if (a & 2)
                c = "px";
            if (a & 4)
                c = "%";
            if (a & 16)
                c = "em";
            var f = a & 8 && U(g);
            a &= -9;
            d = d || (a ? fb : A);
            return function(i, h) {
                b && h && (h *= b);
                var a = d(i, f ? f(i) : g, h, c);
                return b && a != e ? a / b : a
            }
        }
        function C(a) {
            return function(c, b) {
                s(c, a, b)
            }
        }
        var hb = {
            r: ["rotate"],
            sX: ["scaleX", 2],
            sY: ["scaleY", 2],
            tX: ["translateX", 1],
            tY: ["translateY", 1],
            kX: ["skewX"],
            kY: ["skewY"]
        };
        function jb(a) {
            var b = "";
            if (a) {
                j(a, function(d, c) {
                    var a = hb[c];
                    if (a) {
                        var e = a[1] || 0;
                        if (ib[c] != d)
                            b += " " + a[0] + "(" + d + (["deg", "px", ""])[e] + ")"
                    }
                });
                if (B())
                    if (a.tX || a.tY || a.tZ != f)
                        b += " translate3d(" + (a.tX || 0) + "px," + (a.tY || 0) + "px," + (a.tZ || 0) + "px)"
            }
            return b
        }
        function nb(a) {
            return "rect(" + a.y + "px " + a.t + "px " + a.m + "px " + a.x + "px)"
        }
        a.Nh = l("transformOrigin", 8);
        a.Sh = l("backfaceVisibility", 8);
        a.jc = l("transformStyle", 8);
        a.Th = l("perspective", 10);
        a.Eh = l("perspectiveOrigin", 8);
        a.He = function(b, a) {
            O(b, a == 1 ? "" : "scale(" + a + ")")
        }
        ;
        a._AddEvent = function(b, c, d, e) {
            b = a._GetElement(b);
            b.addEventListener(c, d, e)
        }
        ;
        a._RemoveEvent = function(b, c, d, e) {
            b = a._GetElement(b);
            b.removeEventListener(c, d, e)
        }
        ;
        a._CancelEvent = function(a) {
            a = u(a);
            a.preventDefault && a.preventDefault();
            a.cancel = d;
            a.returnValue = k
        }
        ;
        a._StopEvent = function(a) {
            a = u(a);
            a.stopPropagation && a.stopPropagation();
            a.cancelBubble = d
        }
        ;
        a.Z = function(d, c) {
            var a = [].slice.call(arguments, 2)
              , b = function() {
                var b = a.concat([].slice.call(arguments, 0));
                return c.apply(d, b)
            };
            return b
        }
        ;
        a.Uc = function(b, c) {
            if (c == f)
                return b.textContent || b.innerText;
            var d = h.createTextNode(c);
            a.Ob(b);
            b.appendChild(d)
        }
        ;
        a.xg = function(a, b) {
            if (b == f)
                return a.innerHTML;
            a.innerHTML = b
        }
        ;
        a.mh = function(b) {
            var a = b.getBoundingClientRect();
            return {
                x: a.left,
                y: a.top,
                w: a.right - a.left,
                h: a.bottom - a.top,
                t: a.right,
                m: a.bottom
            }
        }
        ;
        a.fb = function(d, c) {
            for (var b = [], a = d.firstChild; a; a = a.nextSibling)
                (c || a.nodeType == 1) && b.push(a);
            return b
        }
        ;
        function zb(a, c, f, b) {
            b = b || "u";
            for (a = a ? a.firstChild : e; a; a = a.nextSibling)
                if (a.nodeType == 1) {
                    if (K(a, b) == c)
                        return a;
                    if (!f) {
                        var d = zb(a, c, f, b);
                        if (d)
                            return d
                    }
                }
        }
        a._FindChild = zb;
        function Y(a, d, h, b) {
            b = b || "u";
            var c = [];
            for (a = a ? a.firstChild : e; a; a = a.nextSibling)
                if (a.nodeType == 1) {
                    (d == f || K(a, b) == d) && c.push(a);
                    if (!h) {
                        var g = Y(a, d, h, b);
                        if (m(g))
                            c = c.concat(g)
                    }
                }
            return c
        }
        a.fh = function(b, a) {
            return b.getElementsByTagName(a)
        }
        ;
        a.gb = function(a, f, d, g) {
            d = d || "u";
            var e;
            do {
                if (a.nodeType == 1) {
                    var c;
                    d && (c = K(a, d));
                    if (c && c == q(f, c) || g == a.tagName) {
                        e = a;
                        break
                    }
                }
                a = b.Mb(a)
            } while (a && a != h.body);
            return e
        }
        ;
        a.Ge = function(a) {
            return db(["INPUT", "TEXTAREA", "SELECT"])[a.tagName]
        }
        ;
        function x() {
            for (var d = arguments, h = 1 & d[0], e = 1 + h, g = d[e - 1] || {}, c, b, a; e < m(d); e++)
                if (c = d[e])
                    for (b in c) {
                        a = c[b];
                        if (a !== f) {
                            a = c[b];
                            var i = g[b];
                            g[b] = h && (D(i) || D(a)) ? x(h, {}, i, a) : a
                        }
                    }
            return g
        }
        a.B = x;
        function cb(f, g) {
            var d = {}, c, a, b;
            for (c in f) {
                a = f[c];
                b = g[c];
                if (a !== b) {
                    var e = k;
                    if (D(a) && D(b)) {
                        a = cb(a, b);
                        e = !Z(a)
                    }
                    !e && (d[c] = a)
                }
            }
            return d
        }
        a.ch = cb;
        a.Ze = function(a, c) {
            if (a) {
                var b;
                j(c, function(c) {
                    if (a[c] != f)
                        (b = b || {})[c] = a[c];
                    delete a[c]
                });
                return b
            }
        }
        ;
        a.Pg = function(a) {
            return N(a) == "function"
        }
        ;
        a.Ye = S;
        a.jh = function(a) {
            return N(a) == "string"
        }
        ;
        a.We = function(a) {
            return !S(a) && !isNaN(r(a)) && isFinite(a)
        }
        ;
        a.f = j;
        a.Ug = Z;
        a.Tg = D;
        function W(a) {
            return h.createElement(a)
        }
        a.Lb = function() {
            return W("DIV")
        }
        ;
        a.Vd = function() {
            return W("SPAN")
        }
        ;
        a.Sc = function(a) {
            return function() {
                return a
            }
        }
        ;
        a.Cc = function() {}
        ;
        a.Sc(d);
        a.Sc(k);
        a.Sc(e);
        function s(b, c, a, d) {
            if (a === f)
                return b.getAttribute(c);
            a = a == e ? "" : d ? a + d : a;
            b.setAttribute(c, a)
        }
        function K(a, b) {
            return s(a, b) || s(a, "data-" + b)
        }
        a.g = s;
        a.cb = K;
        a.s = function(e, c, d) {
            var b = a.Oc(K(e, c));
            if (isNaN(b))
                b = d;
            return b
        }
        ;
        a.Ec = function(b, c, a) {
            return eb(s(b, c), a)
        }
        ;
        function G(b, a) {
            return s(b, "class", a) || ""
        }
        function db(b) {
            var a = {};
            j(b, function(b) {
                if (b != f)
                    a[b] = b
            });
            return a
        }
        function eb(a, b) {
            return a && a.match(b || Eb) || e
        }
        function V(b, a) {
            return db(eb(b || "", a))
        }
        a.Od = db;
        a.Wd = eb;
        a.Wg = function(a) {
            a && (a = a.toLowerCase());
            return a
        }
        ;
        function gb(b, c) {
            var a = "";
            j(c, function(c) {
                a && (a += b);
                a += c
            });
            return a
        }
        function Q(a, c, b) {
            G(a, gb(" ", x(cb(V(G(a)), V(c)), V(b))))
        }
        a.Xd = gb;
        a.Mb = function(a) {
            return a.parentNode
        }
        ;
        a.vc = function(b) {
            a.vb(b, "none")
        }
        ;
        a.tb = function(b, c) {
            a.vb(b, q(c, d) ? "" : "none")
        }
        ;
        a.Zg = function(b, a) {
            b.removeAttribute(a)
        }
        ;
        a.Cg = function(b, a) {
            b.style.clip = nb(a)
        }
        ;
        a.Ub = function() {
            return +new Date
        }
        ;
        a.O = function(b, a) {
            b.appendChild(a)
        }
        ;
        a.Jg = function(c, b) {
            j(b, function(b) {
                a.O(c, b)
            })
        }
        ;
        a.ub = function(b, a, c) {
            (c || a.parentNode).insertBefore(b, a)
        }
        ;
        a.Mg = function(b, a, c) {
            b.insertAdjacentHTML(a, c)
        }
        ;
        a.ib = function(b, a) {
            a = a || b.parentNode;
            a && a.removeChild(b)
        }
        ;
        a.Eg = function(b, c) {
            j(b, function(b) {
                a.ib(b, c)
            })
        }
        ;
        a.Ob = function(b) {
            a.Eg(a.fb(b, d), b)
        }
        ;
        function Bb() {
            j([].slice.call(arguments, 0), function(b) {
                if (a.Ye(b))
                    Bb.apply(e, b);
                else
                    b && b._Destroy && b._Destroy()
            })
        }
        a._Destroy = Bb;
        a.ad = function(b, c) {
            var d = a.Mb(b);
            if (c & 1) {
                a.U(b, (a.I(d) - a.I(b)) / 2);
                a.te(b, e)
            }
            if (c & 2) {
                a.T(b, (a.J(d) - a.J(b)) / 2);
                a.re(b, e)
            }
        }
        ;
        var X = {
            _Top: e,
            _Right: e,
            _Bottom: e,
            _Left: e,
            Cb: e,
            yb: e
        };
        a.Bh = function(b) {
            var c = a.Lb();
            L(c, {
                ye: "block",
                Rb: a.db(b),
                _Top: 0,
                _Left: 0,
                Cb: 0,
                yb: 0
            });
            var e = a.Ae(b, X);
            a.ub(c, b);
            a.O(c, b);
            var f = a.Ae(b, X)
              , d = {};
            j(e, function(b, a) {
                if (b == f[a])
                    d[a] = b
            });
            L(c, X);
            L(c, d);
            L(b, {
                _Top: 0,
                _Left: 0
            });
            return d
        }
        ;
        a.Rh = function(b, a) {
            return parseInt(b, a || 10)
        }
        ;
        a.Oc = r;
        a.Qe = function(b, a) {
            var c = h.body;
            while (a && b !== a && c !== a)
                a = a.parentNode;
            return b === a
        }
        ;
        function ab(e, d, c) {
            var b = e.cloneNode(!d);
            !c && a.Zg(b, "id");
            return b
        }
        a.bb = ab;
        a.Yb = function(f, g) {
            var b = new Image;
            function c(f, d) {
                a._RemoveEvent(b, "load", c);
                a._RemoveEvent(b, "abort", e);
                a._RemoveEvent(b, "error", e);
                g && g(b, d)
            }
            function e(a) {
                c(a, d)
            }
            if (sb() && n < 11.6 || !f)
                c(!f);
            else {
                a._AddEvent(b, "load", c);
                a._AddEvent(b, "abort", e);
                a._AddEvent(b, "error", e);
                b.src = f
            }
        }
        ;
        a.Qh = function(g, c, f) {
            var d = 1;
            function e(a) {
                d--;
                if (c && a && a.src == c.src)
                    c = a;
                !d && f && f(c)
            }
            j(g, function(f) {
                var c = b.cb(f, "src");
                if (c) {
                    d++;
                    a.Yb(c, e)
                }
            });
            e()
        }
        ;
        a.ve = function(a, g, i, h) {
            if (h)
                a = ab(a);
            var c = Y(a, g);
            if (!m(c))
                c = b.fh(a, g);
            for (var f = m(c) - 1; f > -1; f--) {
                var d = c[f]
                  , e = ab(i);
                G(e, G(d));
                b.Lh(e, d.style.cssText);
                b.ub(e, d);
                b.ib(d)
            }
            return a
        }
        ;
        function Lb() {
            var c = this;
            b.V(c, p);
            var e, q = "", t = ["av", "pv", "ds", "dn"], g = [], r, n = 0, l = 0, k = 0;
            function m() {
                Q(e, r, (g[k || l & 2 || l] || "") + " " + (g[n] || ""));
                a.ic(e, k ? "none" : "")
            }
            function d() {
                n = 0;
                c.W(i, "mouseup", d);
                c.W(h, "mouseup", d);
                c.W(h, "touchend", d);
                c.W(h, "touchcancel", d);
                c.W(i, "blur", d);
                m()
            }
            function o() {
                n = 4;
                m();
                c.a(i, "mouseup", d);
                c.a(h, "mouseup", d);
                c.a(h, "touchend", d);
                c.a(h, "touchcancel", d);
                c.a(i, "blur", d)
            }
            c.fe = function(a) {
                if (a === f)
                    return l;
                l = a & 2 || a & 1;
                m()
            }
            ;
            c._Enable = function(a) {
                if (a === f)
                    return !k;
                k = a ? 0 : 3;
                m()
            }
            ;
            c.F = function(f) {
                c._Elmt = e = a._GetElement(f);
                s(e, "data-jssor-button", "1");
                var d = b.Wd(G(e));
                if (d)
                    q = d.shift();
                j(t, function(a) {
                    g.push(q + a)
                });
                r = gb(" ", g);
                g.unshift("");
                c.a(e, "mousedown", o);
                c.a(e, "touchstart", o)
            }
            ;
            b.F(c)
        }
        a.pc = function(a) {
            return new Lb(a)
        }
        ;
        a.oh = function(a, b) {
            return c.Hb(a * a + b * b)
        }
        ;
        a.Q = A;
        l("backgroundColor");
        a.Bb = l("overflow");
        a.ic = l("pointerEvents");
        a.T = l("top", 2);
        a.U = l("left", 2);
        a.hb = l("opacity", 1);
        a.N = l("zIndex", 1);
        function yb(m, n, a, k, b, g, f) {
            if (b) {
                var h = b[0]
                  , d = b[1];
                if (f) {
                    var e = c.l(d * 2, 1)
                      , l = e * (f - 1) + 1;
                    a = (a * l - h) / e;
                    if (a > 0) {
                        d /= e;
                        h = 0;
                        var j = c.H(a) - 1;
                        a = a - j;
                        if (a > d && j < f - 1)
                            a = 1 - a
                    }
                }
                a = (a - h) / d;
                a = c.j(c.l(a, 0), 1)
            }
            if (g) {
                a = a * g;
                var i = c.G(a);
                a - i && (a -= i);
                a = c.j(c.l(a, 0), 1)
            }
            if (b || g)
                a = c.Y(a, 3);
            return m + n * k(a)
        }
        function kb(d, e, h, i) {
            d = d || {};
            var g = {}
              , b = {};
            function n(a) {
                b[a] = d[a]
            }
            function l() {
                b.Nb = d.x;
                h && !e && (b.Nb -= h)
            }
            function m() {
                b.ec = d.y;
                i && !e && (b.ec -= i)
            }
            var k = {
                Nb: 0,
                ec: 0,
                sX: 1,
                sY: 1,
                r: 0,
                rX: 0,
                rY: 0,
                tX: 0,
                tY: 0,
                tZ: 0,
                kX: 0,
                kY: 0
            }
              , c = {};
            if (!B() || e) {
                c.tZ = a.Cc;
                c.tX = a.Cc;
                c.tY = a.Cc
            }
            if (B() || e) {
                c.x = l;
                c.y = m
            }
            j(d, function(b, a) {
                (c[a] || n)(a)
            });
            j(b, function(c, a) {
                if (k[a] != f) {
                    g[a] = c;
                    delete b[a]
                }
            });
            Z(g) && (b.me = g);
            return b
        }
        function vb(f, e) {
            var b = []
              , h = e & 1;
            e & 2 && (h = !h);
            for (var k = c.H(f / 2), a = 0; a < f; a++) {
                var d = a;
                if (e & 4) {
                    var g = c.G(c.Tc() * f);
                    d = q(b[a], a);
                    b[a] = q(b[g], g);
                    b[g] = d
                } else {
                    if (e & 2) {
                        d = a < k ? a * 2 : (f - a - 1) * 2 + 1;
                        b[d] = a
                    }
                    if (e & 32)
                        d = c.G(a / 2) + (a % 2 ? c.H(k) : 0);
                    b[d] = a
                }
            }
            h && b.reverse();
            var i = [];
            j(b, function(b, a) {
                i[b] = a
            });
            return i
        }
        function xb(b, h, e, d) {
            for (var g = [], i = e ? c.H((b + d) / 2) : b, f = 1 / (h * (i - 1) + 1), a = 0; a < b; a++) {
                var j = e ? c.G((a + d) / 2) : a;
                g[a] = [j * h * f, f]
            }
            return g
        }
        function Jb(h, u, e) {
            h = h || {
                d: e._Elmt ? s(e._Elmt, "d") : ""
            };
            var E = e._Easing
              , k = e.Yc || {}
              , g = k.r
              , z = g == 0
              , F = k.dl || 0;
            function x(c, a) {
                var d = c[0]
                  , o = c[1]
                  , e = c[2]
                  , p = c[3]
                  , g = c[4]
                  , q = c[5]
                  , h = c[6]
                  , r = c[7];
                if (a === f)
                    a = .5;
                var b = 1 - a
                  , i = b * d + a * e
                  , s = b * o + a * p
                  , j = b * e + a * g
                  , t = b * p + a * q
                  , k = b * g + a * h
                  , u = b * q + a * r
                  , l = b * i + a * j
                  , v = b * s + a * t
                  , m = b * j + a * k
                  , w = b * t + a * u
                  , n = b * l + a * m
                  , x = b * v + a * w;
                return [[d, o, i, s, l, v, n, x], [n, x, m, w, k, u, h, r]]
            }
            function w(c, g) {
                for (var d = 0, e = 0, a = 0, b = g ? 6 : 0; b < m(c); b += 6) {
                    d += c[b];
                    e += c[b + 1];
                    a++
                }
                return {
                    x: a ? d / a : f,
                    y: a ? e / a : f
                }
            }
            function b(b) {
                var l = m(b)
                  , j = b[0] == b[l - 2] && b[1] == b[l - 1]
                  , g = w(b, j)
                  , k = []
                  , h = [];
                function e(a) {
                    return {
                        x: b[a],
                        y: b[a + 1]
                    }
                }
                function f(j, f, b) {
                    var d = a.Le(j, f);
                    h[b] = a.oh(d.x, d.y);
                    if (!h[b] && b % 6) {
                        var g = b % 6 < 3 ? 2 : -2;
                        d = a.Le(e(b + g), f)
                    }
                    var i = c.zh(d.y, d.x);
                    k[b] = i
                }
                for (var d = 0; d < m(b); d += 6) {
                    var i = e(d);
                    f(i, g, d);
                    f(e(d - 2), i, d - 2);
                    f(e(d + 2), i, d + 2)
                }
                return {
                    kb: b,
                    sb: (m(b) - 2) / 6,
                    qc: g.x,
                    lc: g.y,
                    Rc: k,
                    Qc: h,
                    Hc: j
                }
            }
            function n(o) {
                function i(a) {
                    return a.replace(/[\^\s]*([mhvlzcsqta]|\-?\d*\.?\d+)[,\$\s]*/gi, " _1").replace(/([mhvlzcsqta])/gi, " _1").trim().split("  ").map(l)
                }
                function l(a) {
                    return a.split(" ").map(k)
                }
                function k(a, b) {
                    return b === 0 ? a : +a
                }
                var h = [], a, n = i(o || ""), d, e, f, g;
                function c(b) {
                    f = b[m(b) - 2];
                    g = b[m(b) - 1];
                    a = a.concat(b)
                }
                j(n, function(i) {
                    var j = i.shift();
                    switch (j) {
                    case "M":
                        a && h.push(b(a));
                        a = [];
                        d = i[0];
                        e = i[1];
                        c(i);
                        break;
                    case "L":
                        c([f, g, i[0], i[1], i[0], i[1]]);
                        break;
                    case "C":
                        c(i);
                        break;
                    case "Z":
                    case "z":
                        (f != d || g != e) && c([f, g, d, e, d, e])
                    }
                });
                a && h.push(b(a));
                return h
            }
            function d(a, b) {
                return c.Y(a, 2) + "," + c.Y(b, 2)
            }
            function A(a) {
                for (var c = "M" + d(a[0], a[1]), b = 2; b < m(a) - 2; b += 6) {
                    c += "C";
                    c += d(a[b], a[b + 1]) + " ";
                    c += d(a[b + 2], a[b + 3]) + " ";
                    c += d(a[b + 4], a[b + 5])
                }
                return c
            }
            function y(b) {
                var a = "";
                j(b, function(b) {
                    a += A(b)
                });
                return a
            }
            function D(d) {
                for (var c = [], a = 0; a < m(d) - 2; a += 6)
                    c.push(b(d.slice(a, a + 8)));
                return c
            }
            function B(c) {
                var a = [];
                j(c, function(c, d) {
                    var b = c.kb;
                    !d && a.push(b[0], b[1]);
                    a = a.concat(b.slice(2))
                });
                return b(a)
            }
            function l(d, a) {
                var p = a.uh = []
                  , q = a.Bf = []
                  , e = a.sb + (!d.Hc || !d.Hc);
                function n(b) {
                    return a.Rc[b] - d.Rc[b]
                }
                function h(b, a) {
                    a = a || 0;
                    return c.Y((b - a + c.q * 101) % (c.q * 2) - c.q + a, 8)
                }
                function i(b, f) {
                    var e = d.Qc[b]
                      , g = a.Qc[b]
                      , i = g - e
                      , c = n(b);
                    c = h(c, f);
                    p[b] = i;
                    q[b] = c;
                    return c
                }
                for (var l = 0, b = 0; b < e; b++)
                    l += h(n(b * 6));
                var f = h(l / e);
                if (g) {
                    var j = g > 0 ? 1 : -1;
                    f = (f + c.q * 2 * j) % (c.q * 2) || c.q * 2 * j;
                    f += c.q * 2 * (g - j)
                }
                for (var b = 0; b < m(d.kb); b += 6) {
                    var o = i(b, f);
                    i(b - 2, o);
                    i(b + 2, o)
                }
                var s = xb(e, F)
                  , r = vb(e, k.o);
                a.Dd = function(b, c) {
                    if (b >= 0 && b <= a.sb)
                        return yb(0, 1, c, E, s[r[b % e]])
                }
            }
            function t(d, a, s, n, i) {
                function q(d) {
                    for (var a = [0, .2, 0, .09, .09, 0, .2, 0, .31, 0, .4, .09, .4, .2, .4, .31, .31, .4, .2, .4, .09, .4, 0, .31, 0, .2], c = 0; c < m(a); c += 2) {
                        a[c] += d.qc - .2;
                        a[c + 1] += d.lc - .2
                    }
                    var e = b(a);
                    e.Hc = d.Hc;
                    return e
                }
                d = s[i] = d || q(a);
                a = n[i] = a || q(d);
                var h = d.sb
                  , g = a && a.sb;
                if (h < g)
                    return t(a, d, n, s, i);
                if (g < h) {
                    for (var r = D(a.kb), u = h / g, o = h - g, f = 0, p = 0; p < 10 && f < o; p++) {
                        var v = u + u * p / 10
                          , e = 0;
                        j(r, function(d, g) {
                            e += d.sb;
                            var b = c._Round((g + 1) * v);
                            if (e < b) {
                                var a = c.j(b - e, o - f);
                                d.sb += a;
                                f += a;
                                e += a
                            }
                            return o < f
                        })
                    }
                    var k = [];
                    j(r, function(d) {
                        var a = d.sb
                          , c = d.kb;
                        while (a - 1) {
                            var e = x(c, 1 / a);
                            k.push(b(e[0]));
                            c = e[1];
                            a--
                        }
                        k.push(b(c))
                    });
                    a = n[i] = B(k)
                }
                l(d, a);
                l(a, d)
            }
            function r(b, a) {
                if (m(b) < m(a))
                    return r(a, b);
                j(b, function(d, c) {
                    t(d, a[c], b, a, c)
                })
            }
            var o = n(h.d)
              , p = n(u.d);
            r(o, p);
            function i(b, h, i, e, a, l) {
                var d = b.kb;
                if (a >= 0 && a < m(b.kb)) {
                    var k = h.kb, f, g;
                    if (z) {
                        f = d[a] + (k[a] - d[a]) * e;
                        g = d[a + 1] + (k[a + 1] - d[a + 1]) * e
                    } else {
                        var o = b.Qc[a]
                          , p = h.uh[a]
                          , q = b.Rc[a]
                          , r = h.Bf[a]
                          , j = o + p * e
                          , n = q + r * e;
                        f = j * c.Gb(n) + i.x;
                        g = j * c.eb(n) + i.y
                    }
                    l[a] = f;
                    l[a + 1] = g;
                    return {
                        x: f,
                        y: g
                    }
                }
            }
            var v = {
                E: function(a) {
                    if (!a)
                        return h;
                    if (a == 1)
                        return u;
                    var b = [];
                    j(o, function(c, n) {
                        for (var g = [], d = p[n], e = 0; e < m(c.kb); e += 6) {
                            var f = d.Dd(e / 6, a)
                              , l = q(d.Dd(e / 6 - 1, a), f)
                              , k = q(d.Dd(e / 6 + 1, a), f)
                              , j = {
                                x: c.qc + (d.qc - c.qc) * f,
                                y: c.lc + (d.lc - c.lc) * f
                            }
                              , h = i(c, d, j, f, e, g);
                            i(c, d, h, (f + l) / 2, e - 2, g);
                            i(c, d, h, (f + k) / 2, e + 2, g)
                        }
                        b.push(g)
                    });
                    return {
                        d: y(b)
                    }
                },
                ab: function(a) {
                    return a && a.d || ""
                },
                mb: C("d")
            };
            return v
        }
        function Hb(b) {
            return x({
                mc: a.Pg(b) ? b : g._Linear
            }, b)
        }
        function z(i, u, h, M, o) {
            i = i || {};
            h = x({}, h);
            var ab = h._Elmt, p = {}, W = {}, w, y, r = h.je, P = h.Vb, F = k(Hb(h._Easing)), V = k(h.Yc), X = k(h._During), Z = k(h._Round), Y = k(h.zd, E), G = S(u);
            i = k(i, f, d);
            u = k(u, f, d);
            var U = B()
              , K = o ? {
                c: R,
                me: L,
                pt: Jb,
                bl: Q,
                da: H,
                fc: n(C("fill"), [0, 0, 0, 1]),
                sc: n(C("stroke")),
                cl: n(l("color"), [0, 0, 0, 1]),
                bgc: n(l("backgroundColor")),
                bdc: n(l("borderColor")),
                rp: N
            } : {}
              , s = h.td || o && {
                o: 4,
                so: 4,
                me: 4,
                ls: 4,
                lh: 4,
                sX: 4,
                sY: 4
            };
            function T(c) {
                var d = V[c] || {};
                return b.B({}, h, {
                    _Easing: F[c] || F.mc || e,
                    zd: Y[c] || e,
                    Yc: d,
                    _During: X[c] || e,
                    _Round: Z[c] || e,
                    mf: d.rd,
                    td: a.We(s) ? s : s && s[c],
                    je: 0
                })
            }
            function t(a) {
                return m(a) % 2 ? a.concat(a) : a
            }
            function k(a, c, b) {
                a = M ? kb(a, P, b && h.lf, b && h.of) : a || {};
                return o ? x({}, c, a) : a
            }
            function n(f, d) {
                function c(a) {
                    return a == "transparent" ? e : a || d
                }
                function a(a, b) {
                    a = c(a);
                    b = c(b);
                    if (!a && b) {
                        a = b.slice(0);
                        a[3] = 0
                    }
                    return a || [0, 0, 0, 0]
                }
                return function(c, d, g) {
                    d = a(d, c);
                    c = a(c, d);
                    var e = z(c, d, b.B({
                        td: [0, 0, 0, 4]
                    }, g));
                    return {
                        E: function(a) {
                            return e.E(a)
                        },
                        ab: function(a) {
                            return "rgba(" + a.join(",") + ")"
                        },
                        mb: f
                    }
                }
            }
            function I(b, k, a) {
                b = b || 0;
                var f = a._Easing || g._Linear
                  , e = a._During
                  , i = a._Round
                  , h = a.mf
                  , j = k - b
                  , d = q(a.td, 2);
                return {
                    E: function(a) {
                        return c.Y(yb(b, j, a, f, e, i, h), d)
                    },
                    ab: J,
                    mb: a.zd
                }
            }
            function A(c, d, a, b) {
                return {
                    E: z(c, d, a).E,
                    ab: function(a) {
                        return a.join(",")
                    },
                    mb: b
                }
            }
            function Q(b, c, a) {
                return A(t(b || [0]), t(c), a, C("stdDeviation"))
            }
            function H(a, c, h) {
                var e = m(c);
                c = t(c);
                if (!a) {
                    a = c.slice(0);
                    j(a, function(c, b) {
                        b % 2 && (a[b] = 0)
                    })
                }
                a = t(a);
                for (var d = m(a), f, b = 1; b <= d && b <= e; b++)
                    if (!(d % b) && !(e % b))
                        f = b;
                function g(b) {
                    var a = b;
                    while (m(a) < d * e / f)
                        a = a.concat(b);
                    return a
                }
                return A(g(a), g(c), h, C("stroke-dasharray"))
            }
            function R(b, c, a) {
                return {
                    E: z(b, c, a).E,
                    ab: function(b) {
                        return (b.y || b.x || b.m - a._OriginalHeight || b.t - a._OriginalWidth) && nb(b) || ""
                    },
                    mb: l("clip")
                }
            }
            function L(e, f, c) {
                var a = c.Af, b;
                function d(b) {
                    var d = (b.rX || 0) % 360
                      , e = (b.rY || 0) % 360
                      , f = (b.r || 0) % 360
                      , g = q(b.sX, 1)
                      , h = q(b.sY, 1)
                      , i = q(b.sZ, 1)
                      , c = new v(b.tX,b.tY,b.tZ || 0);
                    a && c._Move(-a.x, -a.y);
                    c._Scale(g, h, i);
                    c.kf(b.kX, b.kY);
                    c._RotateX(d);
                    c._RotateY(e);
                    c.gf(f);
                    a && c._Move(a.x, a.y);
                    c._Move(b.Nb, b.ec);
                    return c
                }
                if (c.Vb) {
                    y = C("transform");
                    b = function(a) {
                        return d(a).Cf()
                    }
                } else {
                    y = O;
                    if (U)
                        b = function(a) {
                            return d(a).yf()
                        }
                        ;
                    else
                        b = jb
                }
                return {
                    E: z(e, f, c).E,
                    mb: y,
                    ab: b || J
                }
            }
            function N() {
                var b = 1e-5;
                return {
                    E: J,
                    ab: J,
                    mb: function(d) {
                        b *= -1;
                        a.hb(d, c.Y(a.hb(d), 4) + b)
                    }
                }
            }
            j(u, function(b, a) {
                var c = i && i[a] || 0;
                if (G || b !== c) {
                    var d = o && K[a] || (D(b) ? z : I);
                    p[a] = d(c, b, T(a))
                }
            });
            w = function(b) {
                var a;
                j(p, function(c, e) {
                    var d = c.E(b);
                    c.mb(c._Elmt || ab, c.ab(d));
                    e == "o" && (a = d)
                });
                return a
            }
            ;
            r && b.f(p, function(a, e) {
                for (var d = [], b = 0; b < r + 1; b++)
                    d[b] = a.ab(a.E(b / r));
                W[e] = d;
                a.E = function(a) {
                    return d[c._Round(a * r)]
                }
                ;
                a.ab = J
            });
            w.E = function(c) {
                var a = x(d, G ? [] : {}, i);
                b.f(p, function(b, d) {
                    a[d] = b.E(c)
                });
                return a
            }
            ;
            return w
        }
        a.zf = z;
        a.ke = vb;
        a.I = l("width", 2);
        a.J = l("height", 2);
        a.te = l("right", 2);
        a.re = l("bottom", 2);
        l("marginLeft", 2);
        l("marginTop", 2);
        a.db = l("position");
        a.vb = l("display");
        a.Lh = function(a, b) {
            if (b != f)
                a.style.cssText = b;
            else
                return a.style.cssText
        }
        ;
        a.xf = function(b, a) {
            if (a === f) {
                a = A(b, "backgroundImage") || "";
                var c = /\burl\s*\(\s*["']?([^"'\r\n,]+)["']?\s*\)/gi.exec(a) || [];
                return c[1]
            }
            A(b, "backgroundImage", a ? "url('" + a + "')" : "")
        }
        ;
        var E;
        a.wf = E = {
            _Opacity: a.hb,
            _Top: a.T,
            _Right: a.te,
            _Bottom: a.re,
            _Left: a.U,
            Cb: a.I,
            yb: a.J,
            Rb: a.db,
            ye: a.vb,
            _ZIndex: a.N,
            o: a.hb,
            x: a.U,
            y: a.T,
            i: a.N,
            dO: l("stroke-dashoffset", 1),
            ls: l("letterSpacing", 16),
            lh: l("lineHeight", 1),
            so: l("startOffset", 4, 100, s)
        };
        a.Ae = function(c, b) {
            var a = {};
            j(b, function(d, b) {
                if (E[b])
                    a[b] = E[b](c)
            });
            return a
        }
        ;
        function L(b, a) {
            j(a, function(c, a) {
                E[a] && E[a](b, c)
            })
        }
        a.rb = L;
        var ib = {
            Nb: 0,
            ec: 0,
            sX: 1,
            sY: 1,
            r: 0,
            rX: 0,
            rY: 0,
            tX: 0,
            tY: 0,
            tZ: 0,
            kX: 0,
            kY: 0
        }
          , Pb = [g._Linear, g._Swing, g._InQuad, g._OutQuad, g._InOutQuad, g._InCubic, g._OutCubic, g._InOutCubic, g._InQuart, g._OutQuart, g._InOutQuart, g._InQuint, g._OutQuint, g._InOutQuint, g._InSine, g._OutSine, g._InOutSine, g._InExpo, g._OutExpo, g._InOutExpo, g._InCirc, g._OutCirc, g._InOutCirc, g._InElastic, g._OutElastic, g._InOutElastic, g._InBack, g._OutBack, g._InOutBack, g._InBounce, g._OutBounce, g._InOutBounce, g._Early, g._Late, g._Mid, g._Mid2, g._Mid3, g._Mid4, g._Mid5, g._Mid6];
        function ub(a) {
            var c;
            if (b.We(a))
                c = Pb[a];
            else if (b.Tg(a)) {
                c = {};
                j(a, function(a, b) {
                    c[b] = ub(a)
                })
            }
            return c || a
        }
        a.ce = ub;
        function m(a) {
            return a.length
        }
        a.u = m;
        a.be = w;
        a.cf = function(l, j, k, p, q) {
            b.hb(l, 1);
            var n = {
                o: {
                    j: 0,
                    l: 1
                }
            }
              , e = {
                x: 0,
                y: 0,
                o: 1,
                r: 0,
                rX: 0,
                rY: 0,
                sX: 1,
                sY: 1,
                tZ: 0,
                kX: 0,
                kY: 0
            };
            function o(c, d, e) {
                var a = b.Vd();
                b.vb(a, "inline-block");
                b.db(a, "relative");
                d ? b.xg(a, c) : b.Uc(a, c);
                b.ub(a, e);
                b.hb(a, k);
                return a
            }
            function h(e) {
                var a = []
                  , c = b.fb(e, d);
                b.f(c, function(e) {
                    if (e.nodeType == 3) {
                        for (var l = b.Uc(e), g, f, i = 0; i <= b.u(l); i++) {
                            var k, m;
                            if (i == b.u(l))
                                m = d;
                            else {
                                var c = l[i];
                                k = /\s/.test(c);
                                c = c == " " ? "&nbsp;" : c;
                                m = !j || f != k
                            }
                            if (m) {
                                if (g) {
                                    var n = o(g, f, e);
                                    (!j || !f) && a.push(n)
                                }
                                g = c
                            } else
                                g += c;
                            f = k
                        }
                        b.ib(e)
                    } else
                        a = a.concat(h(e))
                });
                return a
            }
            function m(n) {
                var j = this
                  , h = b.ke(a, 4)
                  , e = b.Ze(n, ["b", "d", "e", "p", "dr"]) || {}
                  , g = {};
                function i(d, g, f) {
                    var b = f ? a : g
                      , e = 0;
                    if (d.ae & 2) {
                        b = c.H(b / 2);
                        if (!d._Reverse) {
                            e = (b + 1) % 2 * d.dd;
                            b += e
                        }
                    }
                    return b
                }
                function d(d) {
                    var g = d & 1
                      , i = d & 2 || 1
                      , e = 0;
                    if (d & 2)
                        e = a % 2;
                    var f = d == 68 ? h : b.ke(a, d);
                    return {
                        ae: d,
                        dd: e,
                        _Reverse: g,
                        we: f,
                        ue: function(a) {
                            return c.G((f[a] + e * !g) / i)
                        }
                    }
                }
                function l(a, b, f, d) {
                    var e = i(a, b, f)
                      , c = 1 / (d * (e - 1) + 1);
                    return {
                        hc: function(e) {
                            return a.we[e] < b && [a.ue(e) * d * c, c]
                        }
                    }
                }
                function m(a) {
                    return {
                        xe: function(b) {
                            return a.we[b] % 2 ? 1 : -1
                        }
                    }
                }
                function k(b, g, k, h, j) {
                    var d = i(b, g, k)
                      , e = 0;
                    function f(a) {
                        return c.z(1 - a / d, h)
                    }
                    if (b.ae & 2) {
                        d = c.H(a / 2) - b.dd;
                        e = f(d - 1) / 2 * !b.dd
                    }
                    return {
                        ze: function(a) {
                            a = b.ue(a);
                            j && (a = d - a - 1);
                            return f(a) - e
                        }
                    }
                }
                j.Zf = e;
                j.bg = function(o) {
                    var i = g[o];
                    if (!i) {
                        var h = e.p && e.p[o] || {}
                          , y = b.mc(h.dl, .5)
                          , x = h.o || 0
                          , z = h.r || 1
                          , p = h.c
                          , r = h.d
                          , n = b.mc(h.dO, 8)
                          , q = c._Round(a * z)
                          , j = d(x)
                          , w = l(j, q, h.dlc, y)
                          , t = p & 8 ? j : d(p)
                          , v = m(t)
                          , s = n & 8 ? j : d(n)
                          , u = k(s, q, h.dc, r, n == 9);
                        i = g[o] = {
                            hc: w.hc,
                            dg: function(a) {
                                return (p != f ? v.xe(a) : 1) * (r ? u.ze(a) : 1)
                            }
                        }
                    }
                    return i
                }
            }
            var i = h(l)
              , a = b.u(i)
              , g = [];
            b.f(i, function(i, h) {
                var a = []
                  , d = b.B({}, e)
                  , f = b.B({}, e, {
                    o: k
                });
                b.f(p, function(j, l) {
                    var i = {}
                      , o = {}
                      , k = g[l] = g[l] || new m(j);
                    b.f(j, function(l, b) {
                        var m = k.bg(b)
                          , p = m.hc(h);
                        if (p) {
                            var a, g = c.Y(l - d[b], 8);
                            if (g) {
                                g = c.Y(l - e[b], 8);
                                g *= m.dg(h);
                                a = c.Y(g + e[b], 4);
                                var j = n[b];
                                if (j) {
                                    a = c.l(a, j.j);
                                    a = c.j(a, j.l)
                                }
                            } else
                                a = l;
                            if (a - f[b]) {
                                i[b] = a;
                                o[b] = p
                            }
                        }
                    });
                    b.B(d, j);
                    b.B(f, i);
                    if (b.Ug(i)) {
                        b.B(i, k.Zf);
                        i.dr = o;
                        a.push(i)
                    }
                });
                b.u(a) && q(i, a)
            })
        }
    }
    ;
    function p() {
        var a = this, f, e = [], c = [];
        function k(a, b) {
            e.push({
                Qb: a,
                Pb: b
            })
        }
        function j(a, c) {
            b.f(e, function(b, d) {
                b.Qb == a && b.Pb === c && e.splice(d, 1)
            })
        }
        function h() {
            e = []
        }
        function g() {
            b.f(c, function(a) {
                b._RemoveEvent(a.pe, a.Qb, a.Pb, a.oe)
            });
            c = []
        }
        a.ld = function() {
            return f
        }
        ;
        a.a = function(f, d, e, a) {
            b._AddEvent(f, d, e, a);
            c.push({
                pe: f,
                Qb: d,
                Pb: e,
                oe: a
            })
        }
        ;
        a.W = function(f, d, e, a) {
            b.f(c, function(g, h) {
                if (g.pe === f && g.Qb == d && g.Pb === e && g.oe == a) {
                    b._RemoveEvent(f, d, e, a);
                    c.splice(h, 1)
                }
            })
        }
        ;
        a.se = g;
        a._On = a.addEventListener = k;
        a._Off = a.removeEventListener = j;
        a.k = function(a) {
            var c = [].slice.call(arguments, 1);
            b.f(e, function(b) {
                b.Qb == a && b.Pb.apply(i, c)
            })
        }
        ;
        a._Destroy = function() {
            if (!f) {
                f = d;
                g();
                h()
            }
        }
    }
    var l = function(C, F, l, m, L, M) {
        C = C || 0;
        var a = this, t, p, n, o, v, D = 0, O = 1, E, B = 0, h = 0, r = 0, A, j, e, g, u, z, s = [], I = k, J, H = k;
        function R(a) {
            e += a;
            g += a;
            j += a;
            h += a;
            r += a;
            B += a
        }
        function y(C) {
            var k = C;
            if (u)
                if (!z && (k >= g || k < e) || z && k >= e)
                    k = ((k - e) % u + u) % u + e;
            if (!A || v || h != k) {
                var i = c.j(k, g);
                i = c.l(i, e);
                if (l._Reverse)
                    i = g - i + e;
                if (!A || v || i != r) {
                    if (t) {
                        var y = (i - j) / (F || 1), x = t(y), n;
                        if (J) {
                            var o = i > e && i < g;
                            if (o != H)
                                n = H = o
                        }
                        if (!n && x != f) {
                            var p = !x;
                            if (p != I)
                                n = I = p
                        }
                        if (n != f) {
                            n && b.ic(m, "none");
                            !n && b.ic(m, b.g(m, "data-events"))
                        }
                    }
                    var w = r
                      , q = r = i;
                    b.f(s, function(c, d) {
                        var a = !A && z || k <= h ? s[b.u(s) - d - 1] : c;
                        a.M(i - B)
                    });
                    h = k;
                    A = d;
                    a.md(w - j, q - j);
                    a.Mc(w, q)
                }
            }
        }
        function G(a, b, d) {
            b && a._Shift(g);
            if (!d) {
                e = c.j(e, a.Lc() + B);
                g = c.l(g, a.qb() + B)
            }
            s.push(a)
        }
        var w = i.requestAnimationFrame || i.webkitRequestAnimationFrame || i.mozRequestAnimationFrame || i.msRequestAnimationFrame;
        if (b.Ve() && b.Zc() < 7 || !w)
            w = function(a) {
                b._Delay(a, l._Interval)
            }
            ;
        function N() {
            if (p) {
                var c = b.Ub()
                  , d = c - D;
                D = c;
                var a = h + d * o * O;
                if (a * o >= n * o)
                    a = n;
                y(a);
                if (!v && a * o >= n * o)
                    P(E);
                else
                    w(N)
            }
        }
        function x(f, i, j) {
            if (!p) {
                p = d;
                v = j;
                E = i;
                f = c.l(f, e);
                f = c.j(f, g);
                n = f;
                o = n < h ? -1 : 1;
                a.od();
                D = b.Ub();
                w(N)
            }
        }
        function P(b) {
            if (p) {
                v = p = E = k;
                a.sd();
                b && b()
            }
        }
        a._Play = function(a, b, c) {
            x(a ? h + a : g, b, c)
        }
        ;
        a.xc = x;
        a.If = function(a, b) {
            x(g, a, b)
        }
        ;
        a.S = P;
        a.Ud = function() {
            return h
        }
        ;
        a.Td = function() {
            return n
        }
        ;
        a.A = function() {
            return r
        }
        ;
        a.M = y;
        a.Vf = function() {
            y(g, d)
        }
        ;
        a._IsPlaying = function() {
            return p
        }
        ;
        a.Pd = function(a) {
            O = a
        }
        ;
        a._Shift = R;
        a.Xd = G;
        a.L = function(a, b) {
            G(a, 0, b)
        }
        ;
        a.vd = function(a) {
            G(a, 1)
        }
        ;
        a.ud = function(a) {
            g += a
        }
        ;
        a.Lc = function() {
            return e
        }
        ;
        a.qb = function() {
            return g
        }
        ;
        a.Mc = a.od = a.sd = a.md = b.Cc;
        b.Ub();
        a.Mh = function() {
            return t && t.E(1)
        }
        ;
        l = b.B({
            _Interval: 16
        }, l);
        m && (J = b.g(m, "data-inactive"));
        u = l.rc;
        z = l.Ah;
        e = j = C;
        g = C + F;
        l._Elmt = m;
        m && (t = b.zf(L, M, l, d, d))
    };
    var u = i._JssorSlideshowFormations = new function() {
        var i = this
          , e = 0
          , a = 1
          , g = 2
          , f = 3
          , t = 1
          , s = 2
          , u = 4
          , r = 8
          , x = 256
          , y = 512
          , w = 1024
          , v = 2048
          , k = v + t
          , j = v + s
          , p = y + t
          , n = y + s
          , o = x + u
          , l = x + r
          , m = w + u
          , q = w + r;
        function z(a) {
            return (a & s) == s
        }
        function A(a) {
            return (a & u) == u
        }
        function h(b, a, c) {
            c.push(a);
            b[a] = b[a] || [];
            b[a].push(c)
        }
        i._FormationStraight = function(f) {
            for (var d = f._Cols, e = f._Rows, s = f._Assembly, t = f.uc, r = [], a = 0, b = 0, i = d - 1, q = e - 1, g = t - 1, c, b = 0; b < e; b++)
                for (a = 0; a < d; a++) {
                    switch (s) {
                    case k:
                        c = g - (a * e + (q - b));
                        break;
                    case m:
                        c = g - (b * d + (i - a));
                        break;
                    case p:
                        c = g - (a * e + b);
                    case o:
                        c = g - (b * d + a);
                        break;
                    case j:
                        c = a * e + b;
                        break;
                    case l:
                        c = b * d + (i - a);
                        break;
                    case n:
                        c = a * e + (q - b);
                        break;
                    default:
                        c = b * d + a
                    }
                    h(r, c, [b, a])
                }
            return r
        }
        ;
        i._FormationSwirl = function(r) {
            var y = r._Cols, z = r._Rows, C = r._Assembly, x = r.uc, B = [], A = [], v = 0, c = 0, i = 0, s = y - 1, t = z - 1, u, q, w = 0;
            switch (C) {
            case k:
                c = s;
                i = 0;
                q = [g, a, f, e];
                break;
            case m:
                c = 0;
                i = t;
                q = [e, f, a, g];
                break;
            case p:
                c = s;
                i = t;
                q = [f, a, g, e];
                break;
            case o:
                c = s;
                i = t;
                q = [a, f, e, g];
                break;
            case j:
                c = 0;
                i = 0;
                q = [g, e, f, a];
                break;
            case l:
                c = s;
                i = 0;
                q = [a, g, e, f];
                break;
            case n:
                c = 0;
                i = t;
                q = [f, e, g, a];
                break;
            default:
                c = 0;
                i = 0;
                q = [e, g, a, f]
            }
            v = 0;
            while (v < x) {
                u = i + "," + c;
                if (c >= 0 && c < y && i >= 0 && i < z && !A[u]) {
                    A[u] = d;
                    h(B, v++, [i, c])
                } else
                    switch (q[w++ % b.u(q)]) {
                    case e:
                        c--;
                        break;
                    case g:
                        i--;
                        break;
                    case a:
                        c++;
                        break;
                    case f:
                        i++
                    }
                switch (q[w % b.u(q)]) {
                case e:
                    c++;
                    break;
                case g:
                    i++;
                    break;
                case a:
                    c--;
                    break;
                case f:
                    i--
                }
            }
            return B
        }
        ;
        i._FormationZigZag = function(q) {
            var x = q._Cols, y = q._Rows, A = q._Assembly, w = q.uc, u = [], v = 0, c = 0, d = 0, r = x - 1, s = y - 1, z, i, t = 0;
            switch (A) {
            case k:
                c = r;
                d = 0;
                i = [g, a, f, a];
                break;
            case m:
                c = 0;
                d = s;
                i = [e, f, a, f];
                break;
            case p:
                c = r;
                d = s;
                i = [f, a, g, a];
                break;
            case o:
                c = r;
                d = s;
                i = [a, f, e, f];
                break;
            case j:
                c = 0;
                d = 0;
                i = [g, e, f, e];
                break;
            case l:
                c = r;
                d = 0;
                i = [a, g, e, g];
                break;
            case n:
                c = 0;
                d = s;
                i = [f, e, g, e];
                break;
            default:
                c = 0;
                d = 0;
                i = [e, g, a, g]
            }
            v = 0;
            while (v < w) {
                z = d + "," + c;
                if (c >= 0 && c < x && d >= 0 && d < y && typeof u[z] == "undefined") {
                    h(u, v++, [d, c]);
                    switch (i[t % b.u(i)]) {
                    case e:
                        c++;
                        break;
                    case g:
                        d++;
                        break;
                    case a:
                        c--;
                        break;
                    case f:
                        d--
                    }
                } else {
                    switch (i[t++ % b.u(i)]) {
                    case e:
                        c--;
                        break;
                    case g:
                        d--;
                        break;
                    case a:
                        c++;
                        break;
                    case f:
                        d++
                    }
                    switch (i[t++ % b.u(i)]) {
                    case e:
                        c++;
                        break;
                    case g:
                        d++;
                        break;
                    case a:
                        c--;
                        break;
                    case f:
                        d--
                    }
                }
            }
            return u
        }
        ;
        i._FormationStraightStairs = function(i) {
            var u = i._Cols
              , v = i._Rows
              , e = i._Assembly
              , t = i.uc
              , r = []
              , s = 0
              , c = 0
              , d = 0
              , f = u - 1
              , g = v - 1
              , x = t - 1;
            switch (e) {
            case k:
            case n:
            case p:
            case j:
                var a = 0
                  , b = 0;
                break;
            case l:
            case m:
            case o:
            case q:
                var a = f
                  , b = 0;
                break;
            default:
                e = q;
                var a = f
                  , b = 0
            }
            c = a;
            d = b;
            while (s < t) {
                if (A(e) || z(e))
                    h(r, x - s++, [d, c]);
                else
                    h(r, s++, [d, c]);
                switch (e) {
                case k:
                case n:
                    c--;
                    d++;
                    break;
                case p:
                case j:
                    c++;
                    d--;
                    break;
                case l:
                case m:
                    c--;
                    d--;
                    break;
                case q:
                case o:
                default:
                    c++;
                    d++
                }
                if (c < 0 || d < 0 || c > f || d > g) {
                    switch (e) {
                    case k:
                    case n:
                        a++;
                        break;
                    case l:
                    case m:
                    case p:
                    case j:
                        b++;
                        break;
                    case q:
                    case o:
                    default:
                        a--
                    }
                    if (a < 0 || b < 0 || a > f || b > g) {
                        switch (e) {
                        case k:
                        case n:
                            a = f;
                            b++;
                            break;
                        case p:
                        case j:
                            b = g;
                            a++;
                            break;
                        case l:
                        case m:
                            b = g;
                            a--;
                            break;
                        case q:
                        case o:
                        default:
                            a = 0;
                            b++
                        }
                        if (b > g)
                            b = g;
                        else if (b < 0)
                            b = 0;
                        else if (a > f)
                            a = f;
                        else if (a < 0)
                            a = 0
                    }
                    d = b;
                    c = a
                }
            }
            return r
        }
        ;
        i._FormationRectangle = function(f) {
            var d = f._Cols || 1, e = f._Rows || 1, g = [], a, b, i;
            i = c._Round(c.j(d / 2, e / 2)) + 1;
            for (a = 0; a < d; a++)
                for (b = 0; b < e; b++)
                    h(g, i - c.j(a + 1, b + 1, d - a, e - b), [b, a]);
            return g
        }
        ;
        i._FormationRandom = function(d) {
            for (var e = [], a, b = 0; b < d._Rows; b++)
                for (a = 0; a < d._Cols; a++)
                    h(e, c.H(1e5 * c.Tc()) % 13, [b, a]);
            return e
        }
        ;
        i._FormationCircle = function(d) {
            for (var e = d._Cols || 1, f = d._Rows || 1, g = [], a, i = e / 2 - .5, j = f / 2 - .5, b = 0; b < e; b++)
                for (a = 0; a < f; a++)
                    h(g, c._Round(c.Hb(c.z(b - i, 2) + c.z(a - j, 2))), [a, b]);
            return g
        }
        ;
        i._FormationCross = function(d) {
            for (var e = d._Cols || 1, f = d._Rows || 1, g = [], a, i = e / 2 - .5, j = f / 2 - .5, b = 0; b < e; b++)
                for (a = 0; a < f; a++)
                    h(g, c._Round(c.j(c.P(b - i), c.P(a - j))), [a, b]);
            return g
        }
        ;
        i._FormationRectangleCross = function(f) {
            for (var g = f._Cols || 1, i = f._Rows || 1, j = [], a, d = g / 2 - .5, e = i / 2 - .5, k = c.l(d, e) + 1, b = 0; b < g; b++)
                for (a = 0; a < i; a++)
                    h(j, c._Round(k - c.l(d - c.P(b - d), e - c.P(a - e))) - 1, [a, b]);
            return j
        }
    }
    ;
    i._JssorSlideshowRunner = function(n, q, o, r, w, v) {
        var a = this, f, m, i, t = 0, s = r._TransitionsOrder;
        function h(a) {
            var c = {
                _Left: "x",
                _Top: "y",
                _Bottom: "m",
                _Right: "t",
                _Rotate: "r",
                _ScaleX: "sX",
                _ScaleY: "sY",
                _TranslateX: "tX",
                _TranslateY: "tY",
                _SkewX: "kX",
                _SkewY: "kY",
                _Opacity: "o",
                _ZIndex: "i",
                _Clip: "c"
            };
            b.f(a, function(e, d) {
                var b = c[d];
                if (b) {
                    a[b] = e;
                    delete a[d];
                    b == "c" && h(e)
                }
            });
            if (a._Zoom)
                a.sX = a.sY = a._Zoom
        }
        function j(f, e) {
            var a = {
                _Duration: 1,
                _Delay: 0,
                _Cols: 1,
                _Rows: 1,
                _Opacity: 0,
                _Zoom: 0,
                _Clip: 0,
                _Move: k,
                _SlideOut: k,
                _Reverse: k,
                _Formation: u._FormationRandom,
                _Assembly: 1032,
                _ChessMode: {
                    _Column: 0,
                    _Row: 0
                },
                _Easing: g._Linear,
                _Round: {},
                Dc: [],
                _During: {}
            };
            b.B(a, f);
            if (a._Rows == 0)
                a._Rows = c._Round(a._Cols * e);
            a._Easing = b.ce(a._Easing);
            h(a);
            h(a._Easing);
            h(a._During);
            h(a._Round);
            a.uc = a._Cols * a._Rows;
            a.Uf = function(c, b) {
                c /= a._Cols;
                b /= a._Rows;
                var f = c + "x" + b;
                if (!a.Dc[f]) {
                    a.Dc[f] = {
                        w: c,
                        h: b
                    };
                    for (var d = 0; d < a._Cols; d++)
                        for (var e = 0; e < a._Rows; e++)
                            a.Dc[f][e + "," + d] = {
                                y: e * b,
                                t: d * c + c,
                                m: e * b + b,
                                x: d * c
                            }
                }
                return a.Dc[f]
            }
            ;
            if (a._Brother) {
                a._Brother = j(a._Brother, e);
                a._SlideOut = d
            }
            return a
        }
        function p(s, g, a, t, o, n) {
            var h, e, j = a._ChessMode._Column || 0, m = a._ChessMode._Row || 0, i = a.Uf(o, n), p = a._Formation(a), r = a._SlideOut;
            g = b.bb(g);
            b.N(g, 1);
            b.Bb(g, "hidden");
            b.db(g, "absolute");
            v(g, 0, 0);
            !a._Reverse && p.reverse();
            var f = {
                x: a.c & 1,
                t: a.c & 2,
                y: a.c & 4,
                m: a.c & 8
            }
              , q = new l(0,0);
            b.f(p, function(w, v) {
                if (r)
                    v = b.u(p) - v - 1;
                var x = a._Delay * v;
                b.f(w, function(G) {
                    var J = G[0]
                      , I = G[1]
                      , O = J + "," + I
                      , v = k
                      , w = k
                      , z = k;
                    if (j && I % 2) {
                        if (j & 3)
                            v = !v;
                        if (j & 12)
                            w = !w;
                        if (j & 16)
                            z = !z
                    }
                    if (m && J % 2) {
                        if (m & 3)
                            v = !v;
                        if (m & 12)
                            w = !w;
                        if (m & 16)
                            z = !z
                    }
                    var E = w ? f.m : f.y
                      , B = w ? f.y : f.m
                      , D = v ? f.t : f.x
                      , C = v ? f.x : f.t
                      , H = E || B || D || C
                      , A = b.bb(g);
                    e = {
                        x: 0,
                        y: 0,
                        o: 1
                    };
                    h = b.B({}, e);
                    if (a.o)
                        e.o = 2 - a.o;
                    var N = a._Cols * a._Rows > 1 || H;
                    if (a.sX || a.r) {
                        var M = d;
                        if (M) {
                            e.sX = e.sY = a.sX ? a.sX - 1 : 1;
                            h.sX = h.sY = 1;
                            var T = a.r || 0;
                            e.r = T * 360 * (z ? -1 : 1);
                            h.r = 0
                        }
                    }
                    if (N) {
                        var F = i[O];
                        if (H) {
                            var p = {}
                              , y = a._ScaleClip || 1;
                            if (E && B) {
                                p.y = i.h / 2 * y;
                                p.m = -p.y
                            } else if (E)
                                p.m = -i.h * y;
                            else if (B)
                                p.y = i.h * y;
                            if (D && C) {
                                p.x = i.w / 2 * y;
                                p.t = -p.x
                            } else if (D)
                                p.t = -i.w * y;
                            else if (C)
                                p.x = i.w * y;
                            if (a._Move) {
                                var R = (p.x || 0) + (p.t || 0)
                                  , S = (p.y || 0) + (p.m || 0);
                                e.x += R;
                                e.y += S
                            }
                            h.c = F;
                            b.f(p, function(b, a) {
                                p[a] = F[a] + b
                            });
                            e.c = p
                        } else
                            b.Cg(A, F)
                    }
                    var P = v ? 1 : -1
                      , Q = w ? 1 : -1;
                    if (a.x)
                        e.x += o * a.x * P;
                    if (a.y)
                        e.y += n * a.y * Q;
                    var K = {
                        _Elmt: A,
                        _Easing: a._Easing,
                        _During: a._During,
                        _Round: a._Round,
                        _Move: a._Move,
                        Cb: o,
                        yb: n,
                        je: c._Round(a._Duration / 4),
                        _Reverse: !r
                    };
                    e = b.ch(e, h);
                    var L = new l(t + x,a._Duration,K,A,h,e);
                    q.L(L);
                    s.Ff(A)
                })
            });
            q.M(-1);
            return q
        }
        a.Mf = function() {
            var a = 0
              , d = r._Transitions
              , e = b.u(d);
            if (s)
                a = t++ % e;
            else
                a = c.G(c.Tc() * e);
            d[a] && (d[a].ac = a);
            return d[a]
        }
        ;
        a.Zb = function() {
            n.Zb();
            m = e;
            i = e
        }
        ;
        a.vg = function(v, y, w, x, s, k) {
            f = j(s, k);
            var h = x.Ne
              , d = w.Ne
              , e = h
              , g = d
              , r = f
              , b = f._Brother || j({}, k);
            if (!f._SlideOut) {
                e = d;
                g = h
            }
            var l = b._Shift || 0
              , u = c.l(l, 0)
              , t = c.l(-l, 0);
            m = p(n, g, b, u, q, o);
            i = p(n, e, r, t, q, o);
            a.ac = v
        }
        ;
        a.Sg = function() {
            var a = e;
            if (i) {
                a = new l(0,0);
                a.L(i);
                a.L(m);
                a.ge = f
            }
            return a
        }
    }
    ;
    var o = {
        wh: "data-scale",
        zb: "data-autocenter",
        qd: "data-nofreeze",
        Rd: "data-nodrag"
    }
      , q = new function() {
        var a = this;
        a.Bc = function(c, a, e, d) {
            (d || !b.g(c, a)) && b.g(c, a, e)
        }
        ;
        a.Ac = function(a) {
            var c = b.s(a, o.zb);
            b.ad(a, c)
        }
    }
      , s = {
        Fc: 1
    };
    i._JssorBulletNavigator = function() {
        var a = this, E = b.V(a, p), h, v, C, B, m, l = 0, g, r, n, z, A, i, k, u, t, x, j;
        function y(a) {
            j[a] && j[a].fe(a == l)
        }
        function w(b) {
            a.k(s.Fc, b * r)
        }
        a.Ed = function(a) {
            if (a != m) {
                var d = l
                  , b = c.G(a / r);
                l = b;
                m = a;
                y(d);
                y(b)
            }
        }
        ;
        a.ed = function(a) {
            b.tb(h, !a)
        }
        ;
        a.jd = function(J) {
            b._Destroy(j);
            m = f;
            a.se();
            x = [];
            j = [];
            b.Ob(h);
            v = c.H(J / r);
            l = 0;
            var F = u + z
              , G = t + A
              , s = c.H(v / n) - 1;
            C = u + F * (!i ? s : n - 1);
            B = t + G * (i ? s : n - 1);
            b.I(h, C);
            b.J(h, B);
            for (var o = 0; o < v; o++) {
                var H = b.Vd();
                b.Uc(H, o + 1);
                var p = b.ve(k, "numbertemplate", H, d);
                b.db(p, "absolute");
                var E = o % (s + 1)
                  , I = c.G(o / (s + 1))
                  , y = g.Jb && !i ? s - E : E;
                b.U(p, (!i ? y : I) * F);
                b.T(p, (i ? y : I) * G);
                b.O(h, p);
                x[o] = p;
                g._ActionMode & 1 && a.a(p, "click", b.Z(e, w, o));
                g._ActionMode & 2 && a.a(p, "mouseenter", b.Z(e, w, o));
                j[o] = b.pc(p)
            }
            q.Ac(h)
        }
        ;
        a.F = function(d, c) {
            a._Elmt = h = b._GetElement(d);
            a.id = g = b.B({
                _SpacingX: 10,
                _SpacingY: 10,
                _ActionMode: 1
            }, c);
            k = b._FindChild(h, "prototype");
            u = b.I(k);
            t = b.J(k);
            b.ib(k, h);
            r = g._Steps || 1;
            n = g._Rows || 1;
            z = g._SpacingX;
            A = g._SpacingY;
            i = g._Orientation & 2;
            g._AutoCenter && q.Bc(h, o.zb, g._AutoCenter)
        }
        ;
        a._Destroy = function() {
            b._Destroy(j, E)
        }
        ;
        b.F(a)
    }
    ;
    i._JssorArrowNavigator = function() {
        var a = this, v = b.V(a, p), f, c, g, l, r, k, h, m, j, i;
        function n(b) {
            a.k(s.Fc, b, d)
        }
        function u(a) {
            b.tb(f, !a);
            b.tb(c, !a)
        }
        function t() {
            j._Enable((g._Loop || !l.hh(h)) && k > 1);
            i._Enable((g._Loop || !l.ih(h)) && k > 1)
        }
        a.Ed = function(c, a, b) {
            h = a;
            !b && t()
        }
        ;
        a.ed = u;
        a.jd = function(g) {
            k = g;
            h = 0;
            if (!r) {
                a.a(f, "click", b.Z(e, n, -m));
                a.a(c, "click", b.Z(e, n, m));
                j = b.pc(f);
                i = b.pc(c);
                b.g(f, o.qd, 1);
                b.g(c, o.qd, 1);
                r = d
            }
        }
        ;
        a.F = function(e, d, h, i) {
            a.id = g = b.B({
                _Steps: 1
            }, h);
            f = e;
            c = d;
            if (g.Jb) {
                f = d;
                c = e
            }
            m = g._Steps;
            l = i;
            if (g._AutoCenter) {
                q.Bc(f, o.zb, g._AutoCenter);
                q.Bc(c, o.zb, g._AutoCenter)
            }
            q.Ac(f);
            q.Ac(c)
        }
        ;
        a._Destroy = function() {
            b._Destroy(j, i, v)
        }
        ;
        b.F(a)
    }
    ;
    i._JssorThumbnailNavigator = function() {
        var i = this, E = b.V(i, p), h, B, a, y, C, m, l = [], A, z, g, n, r, w, v, x, t, u;
        function D() {
            var c = this;
            b.V(c, p);
            var h, f, n, l;
            function o() {
                n.fe(m == h)
            }
            function j(e) {
                if (e || !t._LastDragSucceeded()) {
                    var c = g - h % g
                      , a = t.Kd((h + c) / g - 1)
                      , b = a * g + g - c;
                    if (a < 0)
                        b += y % g;
                    if (a >= C)
                        b -= y % g;
                    i.k(s.Fc, b, k, d)
                }
            }
            c.Md = o;
            c.F = function(g, i) {
                c.ac = h = i;
                l = g.ug || g.Bg || b.Lb();
                c.Xc = f = b.ve(u, "thumbnailtemplate", l, d);
                n = b.pc(f);
                a._ActionMode & 1 && c.a(f, "click", b.Z(e, j, 0));
                a._ActionMode & 2 && c.a(f, "mouseenter", b.Z(e, j, 1))
            }
            ;
            b.F(c)
        }
        i.Ed = function(a, e, d) {
            if (a != m) {
                var b = m;
                m = a;
                b != -1 && l[b].Md();
                l[a] && l[a].Md()
            }
            !d && t._PlayTo(t.Kd(c.G(a / g)))
        }
        ;
        i.ed = function(a) {
            b.tb(h, !a)
        }
        ;
        i.jd = function(I, J) {
            b._Destroy(t, l);
            m = f;
            l = [];
            var K = b.bb(B);
            b.Ob(h);
            a.Jb && b.g(h, "dir", "rtl");
            b.Jg(h, b.fb(K));
            var i = b._FindChild(h, "slides", d);
            y = I;
            C = c.H(y / g);
            m = -1;
            var e = a._Orientation & 1
              , s = w + (w + n) * (g - 1) * (1 - e)
              , p = v + (v + r) * (g - 1) * e
              , E = (e ? c.l : c.j)(A, s)
              , u = (e ? c.j : c.l)(z, p);
            x = c.H((A - n) / (w + n) * e + (z - r) / (v + r) * (1 - e));
            var G = s + (s + n) * (x - 1) * e
              , F = p + (p + r) * (x - 1) * (1 - e);
            E = c.j(E, G);
            u = c.j(u, F);
            b.I(i, E);
            b.J(i, u);
            b.ad(i, 3);
            var o = [];
            b.f(J, function(k, f) {
                var h = new D(k,f)
                  , d = h.Xc
                  , a = c.G(f / g)
                  , j = f % g;
                b.U(d, (w + n) * j * (1 - e));
                b.T(d, (v + r) * j * e);
                if (!o[a]) {
                    o[a] = b.Lb();
                    b.O(i, o[a])
                }
                b.O(o[a], d);
                l.push(h)
            });
            var H = b.B({
                _AutoPlay: 0,
                _NaviQuitDrag: k,
                _SlideWidth: s,
                _SlideHeight: p,
                _SlideSpacing: n * e + r * (1 - e),
                _MinDragOffsetToSlide: 12,
                _SlideDuration: 200,
                _PauseOnHover: 1,
                _Cols: x,
                _PlayOrientation: a._Orientation,
                _DragOrientation: a._NoDrag || a._DisableDrag ? 0 : a._Orientation
            }, a);
            t = new j(h,H);
            q.Ac(h)
        }
        ;
        i.F = function(j, f, e) {
            h = j;
            i.id = a = b.B({
                _SpacingX: 0,
                _SpacingY: 0,
                _Orientation: 1,
                _ActionMode: 1
            }, f);
            A = b.I(h);
            z = b.J(h);
            var c = b._FindChild(h, "slides", d);
            u = b._FindChild(c, "prototype");
            e = b.bb(e);
            b.ub(e, c);
            w = b.I(u);
            v = b.J(u);
            b.ib(u, c);
            b.db(c, "absolute");
            b.Bb(c, "hidden");
            g = a._Rows || 1;
            n = a._SpacingX;
            r = a._SpacingY;
            a._AutoCenter &= a._Orientation;
            a._AutoCenter && q.Bc(h, o.zb, a._AutoCenter);
            B = b.bb(h)
        }
        ;
        i._Destroy = function() {
            b._Destroy(t, l, E)
        }
        ;
        b.F(i)
    }
    ;
    function n(e, d, c) {
        var a = this;
        b.V(a, p);
        l.call(a, 0, c._Idle);
        a.zc = 0;
        a.cd = c._Idle
    }
    n.yc = 21;
    n.Wb = 24;
    var t = i._JssorCaptionSlideo = function() {
        var a = this
          , db = b.V(a, p);
        l.call(a, 0, 0);
        var f, j, B, C, w = new l(0,0), L = [], u = [], F, q = 0;
        function H(c, f) {
            var a = L[c];
            if (a == e) {
                a = L[c] = {
                    nb: c,
                    Vc: [],
                    Ce: []
                };
                var d = 0;
                !b.f(u, function(a, b) {
                    d = b;
                    return a.nb > c
                }) && d++;
                u.splice(d, 0, a)
            }
            return a
        }
        function Q(f, v) {
            var s = b.I(f), r = b.J(f), m = b.Vb(f), j = {
                x: m ? 0 : b.U(f),
                y: m ? 0 : b.T(f),
                Nb: 0,
                ec: 0,
                o: b.hb(f),
                i: b.N(f) || 0,
                r: 0,
                rX: 0,
                rY: 0,
                sX: 1,
                sY: 1,
                tX: 0,
                tY: 0,
                tZ: 0,
                kX: 0,
                kY: 0,
                ls: 0,
                lh: 1,
                so: 0,
                c: {
                    y: 0,
                    t: s,
                    m: r,
                    x: 0
                }
            }, a, g;
            if (C) {
                var o = C[b.s(f, "c")];
                if (o) {
                    a = H(o.r, 0);
                    a.Ih = o.e || 0
                }
            }
            var h = b.g(f, "data-to");
            if (h && m) {
                h = b.Wd(h);
                h = {
                    x: b.Oc(h[0]),
                    y: b.Oc(h[1])
                }
            }
            var u = {
                _Elmt: f,
                _OriginalWidth: s,
                _OriginalHeight: r,
                Af: h,
                lf: j.x,
                of: j.y,
                Vb: m
            };
            b.f(v, function(e) {
                var m = b.B({
                    _Easing: b.ce(e.e),
                    _During: e.dr,
                    Yc: e.p
                }, u)
                  , i = b.B(d, {}, e);
                b.Ze(i, ["b", "d", "e", "p", "dr"]);
                var h = new l(e.b,e.d,m,f,j,i);
                q = c.l(q, e.b + e.d);
                if (a) {
                    if (!g)
                        g = new l(e.b,0);
                    g.L(h)
                } else {
                    var k = H(e.b, e.b + e.d);
                    k.Vc.push(h)
                }
                j = h.Mh()
            });
            if (a && g) {
                g.Vf();
                var i = g, n, k = g.Lc(), p = g.qb(), t = c.l(p, a.Ih);
                if (a.nb < p) {
                    if (a.nb > k) {
                        i = new l(k,a.nb - k);
                        i.L(g, d)
                    } else
                        i = e;
                    n = new l(a.nb,t - k,{
                        rc: t - a.nb,
                        Ah: d
                    });
                    n.L(g, d)
                }
                i && a.Vc.push(i);
                n && a.Ce.push(n)
            }
            return j
        }
        function N(d, c) {
            b.f(d, function(d) {
                var f = b.s(d, "play");
                if (c && f) {
                    var e = new t(d,j,{
                        Fe: f
                    });
                    E.push(e);
                    a.a(e, n.yc, I);
                    a.a(e, n.Wb, G)
                } else {
                    N(b.fb(d).concat(b.oc(b.Ec(d, "data-tchd"))), c + 1);
                    var g = b.oc(b.Ec(d, "data-tsep"));
                    g.push(d);
                    b.f(g, function(c) {
                        var a = B[b.s(c, "t")];
                        a && Q(c, a)
                    })
                }
            })
        }
        function cb(f, e, g) {
            var c = f.b - e;
            if (c) {
                var b = new l(e,c);
                b.L(w, d);
                b._Shift(g);
                a.L(b)
            }
            a.ud(f.d);
            return c
        }
        function bb(e) {
            var c = w.Lc()
              , d = 0;
            b.f(e, function(e, f) {
                e = b.B({
                    d: 3e3
                }, e);
                cb(e, c, d);
                c = e.b;
                d += e.d;
                if (!f || e.t == 2) {
                    a.zc = c;
                    a.cd = c + e.d
                }
            })
        }
        function A(i, d, e) {
            var f = b.u(d);
            if (f > 4)
                for (var j = c.H(f / 4), a = 0; a < j; a++) {
                    var g = d.slice(a * 4, c.j(a * 4 + 4, f))
                      , h = new l(g[0].nb,0);
                    A(h, g, e);
                    i.L(h)
                }
            else
                b.f(d, function(a) {
                    b.f(e ? a.Ce : a.Vc, function(a) {
                        e && a.ud(q - a.qb());
                        i.L(a)
                    })
                })
        }
        var i, M, y = 0, g, x, S, R, z, E = [], O = [], r, D, m;
        function v(a) {
            return a & 2 || a & 4 && b.Ad().wd
        }
        function Z() {
            if (!z) {
                g & 8 && a.a(h, "keydown", J);
                if (g & 32) {
                    a.a(h, "mousedown", s);
                    a.a(h, "touchstart", s)
                }
                z = d
            }
        }
        function Y() {
            a.W(h, "keydown", J);
            a.W(h, "mousedown", s);
            a.W(h, "touchstart", s);
            z = k
        }
        function T(b) {
            if (!r || b) {
                r = d;
                a.S();
                b && y && a.M(0);
                a.Pd(1);
                a.If();
                Z();
                a.k(n.yc, a)
            }
        }
        function o() {
            if (!D && (r || a.A())) {
                D = d;
                a.S();
                a.Ud() > a.zc && a.M(a.zc);
                a.Pd(S || 1);
                a.xc(0)
            }
        }
        function V() {
            !m && o()
        }
        function U(c) {
            var b = c;
            if (c < 0 && a.A())
                b = 1;
            if (b != y) {
                y = b;
                M && a.k(n.Wb, a, y)
            }
        }
        function J(a) {
            g & 8 && b.Be(a) == 27 && o()
        }
        function X(a) {
            if (m && b.Ee(a) !== e) {
                m = k;
                g & 16 && b._Delay(V, 160)
            }
        }
        function s(a) {
            g & 32 && !b.Qe(f, b._EvtSrc(a)) && o()
        }
        function W(a) {
            if (!m) {
                m = d;
                if (i & 1)
                    b.Te(a, f) && T()
            }
        }
        function ab(j) {
            var h = b._EvtSrc(j)
              , a = b.gb(h, e, e, "A")
              , c = a && (b.Ge(a) || a === f || b.Qe(f, a));
            if (r && v(g))
                !c && o();
            else if (v(i))
                !c && T(d)
        }
        function I(b) {
            var c = b.Dh()
              , a = O[c];
            a !== b && a && a.Ph();
            O[c] = b
        }
        function G(b, c) {
            a.k(n.Wb, b, c)
        }
        a.Dh = function() {
            return R || ""
        }
        ;
        a.Ph = o;
        a.od = function() {
            U(1)
        }
        ;
        a.sd = function() {
            r = k;
            D = k;
            U(-1);
            !a.A() && Y()
        }
        ;
        a.Mc = function() {
            !m && x && a.Ud() > a.cd && o()
        }
        ;
        a.F = function(m, k, e) {
            f = m;
            j = k;
            i = e.Fe;
            F = e.nh;
            B = j._Transitions;
            C = j._Controls;
            N([f], 0);
            A(w, u);
            if (i) {
                a.L(w);
                F = d;
                x = b.s(f, "idle");
                g = b.s(f, "rollback");
                S = b.s(f, "speed", 1);
                R = b.cb(f, "group");
                (v(i) || v(g)) && a.a(f, "click", ab);
                if ((i & 1 || x) && !b.Ad().wd) {
                    a.a(f, "mouseenter", W);
                    a.a(f, "mouseleave", X)
                }
                M = b.s(f, "pause")
            }
            var l = j._Breaks || []
              , c = l[b.s(f, "b")] || []
              , h = {
                b: q,
                d: b.u(c) ? 0 : e._Idle || x || 0
            };
            c = c.concat([h]);
            bb(c);
            a.qb();
            F && a.ud(1e8);
            q = a.qb();
            A(a, u, d);
            a.M(-1);
            a.M(b.s(f, "initial") || 0)
        }
        ;
        a._Destroy = function() {
            b._Destroy(db, E);
            a.S();
            a.M(-1)
        }
        ;
        b.F(a)
    }
      , j = i._JssorSlider = (i.module || {}).exports = function() {
        var a = this, Gc = b.V(a, p), Ob = "data-jssor-slider", ic = "data-jssor-thumb", u, m, S, Cb, kb, jb, X, J, O, M, Zb, Cc, Hc = 1, Bc = 1, kc = 1, sc = 1, nc = {}, w, R, Mb, bc, Yb, wb, zb, yb, ab, H = [], Rb, r = -1, tc, q, I, G, P, ob, pb, E, N, lb, T, z, W, nb, Z = [], vc, xc, oc, t, vb, Hb, qb, eb, Y, Kb, Gb, Qb, Sb, F, Lb = 0, cb = 0, Q = Number.MAX_VALUE, K = Number.MIN_VALUE, C, mb, db, U = 1, Xb = 0, fb, y, Fb, Eb, L, Ab, Db, B, V, rb, A, Bb, cc = b.Ad(), Vb = cc.wd, x = [], D, hb, bb, Nb, hc, mc, ib;
        function Jb() {
            return !U && Y & 12
        }
        function Ic() {
            return Xb || !U && Y & 3
        }
        function Ib() {
            return !y && !Jb() && !A._IsPlaying()
        }
        function Wc() {
            return !Ic() && Ib()
        }
        function jc() {
            return z || S
        }
        function Pc() {
            return jc() & 2 ? pb : ob
        }
        function lc(a, c, d) {
            b.U(a, c);
            b.T(a, d)
        }
        function Fc(c, b) {
            var a = jc()
              , d = (ob * b + Lb) * (a & 1)
              , e = (pb * b + Lb) * (a & 2) / 2;
            lc(c, d, e)
        }
        function dc(b, f) {
            if (y && !(C & 1)) {
                var e = b, d;
                if (b < K) {
                    e = K;
                    d = -1
                }
                if (b > Q) {
                    e = Q;
                    d = 1
                }
                if (d) {
                    var a = b - e;
                    if (f) {
                        a = c.sh(a) * 2 / c.q;
                        a = c.z(a * d, 1.6)
                    } else {
                        a = c.z(a * d, .625);
                        a = c.qe(a * c.q / 2)
                    }
                    b = e + a * d
                }
            }
            return b
        }
        function qc(a) {
            return dc(a, d)
        }
        function Nc(a) {
            return dc(a)
        }
        function xb(a, b) {
            if (!(C & 1)) {
                var c = a - Q + (b || 0)
                  , d = K - a + (b || 0);
                if (c > 0 && c > d)
                    a = Q;
                else if (d > 0)
                    a = K
            }
            return a
        }
        function yc(a) {
            return !(C & 1) && a - K < .0001
        }
        function wc(a) {
            return !(C & 1) && Q - a < .0001
        }
        function sb(a) {
            return !(C & 1) && (a - K < .0001 || Q - a < .0001)
        }
        function Tb(c, a, d) {
            !ib && b.f(Z, function(b) {
                b.Ed(c, a, d)
            })
        }
        function ec(b) {
            var a = b
              , d = sb(b);
            if (d)
                a = xb(a);
            else {
                b = v(b);
                a = b
            }
            a = c.G(a);
            a = c.l(a, 0);
            return a
        }
        function fd(a) {
            x[r];
            Rb = r;
            r = a;
            tc = x[r]
        }
        function zc() {
            z = 0;
            var b = B.A()
              , d = ec(b);
            Tb(d, b);
            if (sb(b) || b == c.G(b)) {
                if (t & 2 && (eb > 0 && d == q - 1 || eb < 0 && !d))
                    t = 0;
                fd(d);
                a.k(j._EVT_PARK, r, Rb)
            }
        }
        function pc(a, b) {
            if (q && (!b || !A._IsPlaying())) {
                A.S();
                realPosition = qc(a);
                V.M(realPosition);
                zc()
            }
        }
        function ub(a) {
            if (q) {
                a = xb(a);
                a = v(a);
                fb = k;
                _IsStandBy = k;
                y = k;
                pc(a)
            } else
                Tb(0, 0)
        }
        function Zc() {
            var b = j.Se || 0
              , a = mb;
            j.Se |= a;
            return W = a & ~b
        }
        function Uc() {
            if (W) {
                j.Se &= ~mb;
                W = 0
            }
        }
        function Dc(c) {
            var a = b.Lb();
            b.rb(a, ab);
            c && b.Bb(a, "hidden");
            return a
        }
        function v(b, a) {
            a = a || q || 1;
            return (b % a + a) % a
        }
        function fc(c, a, b) {
            t & 8 && (t = 0);
            tb(c, Gb, a, b)
        }
        function Ub() {
            b.f(Z, function(a) {
                a.ed(a.id._ChanceToShow <= U)
            })
        }
        function Mc(c) {
            if (!U && (b.Ee(c) || !b.Te(c, u))) {
                U = 1;
                Ub();
                if (!y) {
                    Y & 12 && Jc();
                    x[r] && x[r].kc()
                }
                a.k(j._EVT_MOUSE_LEAVE)
            }
        }
        function Lc() {
            if (U) {
                U = 0;
                Ub();
                y || !(Y & 12) || Kc()
            }
            a.k(j._EVT_MOUSE_ENTER)
        }
        function Wb(b, a) {
            tb(b, a, d)
        }
        function tb(g, h, l, p) {
            if (q && (!y || m._NaviQuitDrag) && !Jb() && !isNaN(g)) {
                var e = B.A()
                  , a = g;
                if (l) {
                    a = e + g;
                    if (C & 2) {
                        if (yc(e) && g < 0)
                            a = Q;
                        if (wc(e) && g > 0)
                            a = K
                    }
                }
                if (!(C & 1))
                    if (p)
                        a = v(a);
                    else
                        a = xb(a, .5);
                if (l && !sb(a))
                    a = c._Round(a);
                var i = (a - e) % q;
                a = e + i;
                if (h == f)
                    h = Gb;
                var b = c.P(i)
                  , j = 0;
                if (b) {
                    if (b < 1)
                        b = c.z(b, .5);
                    if (b > 1) {
                        var o = Pc()
                          , n = (S & 1 ? zb : yb) / o;
                        b = c.j(b, n * 1.5)
                    }
                    j = h * b
                }
                ib = d;
                A.S();
                ib = k;
                A.Re(e, a, j)
            }
        }
        function Rc(e, h, n) {
            var l = this, i = {
                _Top: 2,
                _Right: 1,
                _Bottom: 2,
                _Left: 1
            }, m = {
                _Top: "top",
                _Right: "right",
                _Bottom: "bottom",
                _Left: "left"
            }, g, a, f, j, k = {};
            l._Elmt = e;
            l._ScaleSize = function(q, p, t) {
                var l, s = q, r = p;
                if (!f) {
                    f = b.Bh(e);
                    g = e.parentNode;
                    j = {
                        _Scale: b.s(e, o.wh, 1),
                        _AutoCenter: b.s(e, o.zb)
                    };
                    b.f(m, function(c, a) {
                        k[a] = b.s(e, "data-scale-" + c, 1)
                    });
                    a = e;
                    if (h) {
                        a = b.bb(g, d);
                        b.rb(a, {
                            _Top: 0,
                            _Left: 0
                        });
                        b.O(a, e);
                        b.O(g, a)
                    }
                }
                if (n) {
                    l = c.l(q, p);
                    if (h)
                        if (t >= 0 && t < 1) {
                            var v = c.j(q, p);
                            l = c.j(l / v, 1 / (1 - t)) * v
                        }
                } else
                    s = r = l = c.z(O < M ? p : q, j._Scale);
                l *= h && (l != 1 || b.Ve()) ? 1.001 : 1;
                h && (sc = l);
                b.He(a, l);
                b.I(g, f.Cb * s);
                b.J(g, f.yb * r);
                var u = b.Ue() && b.Zc() < 9 ? l : 1
                  , w = (s - u) * f.Cb / 2
                  , x = (r - u) * f.yb / 2;
                b.U(a, w);
                b.T(a, x);
                b.f(f, function(d, a) {
                    if (i[a] && d) {
                        var e = (i[a] & 1) * c.z(q, k[a]) * d + (i[a] & 2) * c.z(p, k[a]) * d / 2;
                        b.wf[a](g, e)
                    }
                });
                b.ad(g, j._AutoCenter)
            }
        }
        function dd() {
            var a = this;
            l.call(a, 0, 0, {
                rc: q
            });
            b.f(x, function(b) {
                a.vd(b);
                b._Shift(F / E)
            })
        }
        function cd() {
            var a = this
              , b = Bb._Elmt;
            l.call(a, -1, 2, {
                _Easing: g._Linear,
                zd: {
                    Rb: Fc
                },
                rc: q,
                _Reverse: Hb
            }, b, {
                Rb: 1
            }, {
                Rb: -1
            });
            a.Xc = b
        }
        function ed() {
            var b = this;
            l.call(b, -1e8, 2e8);
            b.Mc = function(e, b) {
                if (c.P(b - e) > 1e-5) {
                    var g = b
                      , f = b;
                    if (c.G(b) != b && b > e && (C & 1 || b > cb))
                        f++;
                    var h = ec(f);
                    Tb(h, g, d);
                    a.k(j._EVT_POSITION_CHANGE, v(g), v(e), b, e)
                }
            }
        }
        function Tc(o, n) {
            var b = this, g, i, f, c, h;
            l.call(b, -1e8, 2e8, {});
            b.od = function() {
                fb = d;
                a.k(j._EVT_SWIPE_START, v(B.A()), V.A())
            }
            ;
            b.sd = function() {
                fb = k;
                c = k;
                a.k(j._EVT_SWIPE_END, v(B.A()), V.A());
                !y && zc()
            }
            ;
            b.Mc = function(e, b) {
                var a = b;
                if (c)
                    a = h;
                else if (f) {
                    var d = b / f;
                    a = m._SlideEasing(d) * (i - g) + g
                }
                a = qc(a);
                V.M(a)
            }
            ;
            b.Re = function(a, c, h, e) {
                y = k;
                f = h || 1;
                g = a;
                i = c;
                ib = d;
                V.M(a);
                ib = k;
                b.M(0);
                b.xc(f, e)
            }
            ;
            b.nf = function() {
                c = d;
                c && b._Play(e, e, d)
            }
            ;
            b.vf = function(a) {
                h = a
            }
            ;
            V = new ed;
            V.L(o);
            Sb && V.L(n)
        }
        function Qc() {
            var c = this
              , a = Dc();
            b.N(a, 0);
            c._Elmt = a;
            c.Ff = function(c) {
                b.O(a, c);
                b.tb(a)
            }
            ;
            c.Zb = function() {
                b.vc(a);
                b.Ob(a)
            }
        }
        function bd(w, h) {
            var g = this, hb = b.V(g, p), z, H = 0, V, y, u, F, K, o, E = [], U, M, J, i, s, A, S;
            l.call(g, -N, N + 1, {
                rc: C & 1 ? q : f,
                _Reverse: Hb
            });
            function Q() {
                z && z._Destroy();
                Xb -= H;
                H = 0;
                z = new kb._Class(y,kb,{
                    _Idle: b.s(y, "idle", Kb),
                    nh: !t
                });
                z._On(n.Wb, X)
            }
            function X(b, a) {
                H += a;
                Xb += a;
                if (h == r)
                    !H && g.kc()
            }
            function P(p, s, n) {
                if (!M) {
                    M = d;
                    if (o && n) {
                        var q = b.s(o, "data-expand", 0) * 2
                          , f = n.width
                          , e = n.height
                          , l = f
                          , i = e;
                        if (f && e) {
                            if (F) {
                                if (F & 3 && (!(F & 4) || f > I || e > G)) {
                                    var m = k
                                      , r = I / G * e / f;
                                    if (F & 1)
                                        m = r > 1;
                                    else if (F & 2)
                                        m = r < 1;
                                    l = m ? f * G / e : I;
                                    i = m ? G : e * I / f
                                }
                                b.I(o, l);
                                b.J(o, i);
                                b.T(o, (G - i) / 2);
                                b.U(o, (I - l) / 2)
                            }
                            b.He(o, c.l((l + q) / l, (i + q) / i))
                        }
                        b.db(o, "absolute")
                    }
                    a.k(j._EVT_LOAD_END, h)
                }
                s.Ie(k);
                p && p(g)
            }
            function W(f, b, c, e) {
                if (e == A && r == h && t && Ib() && !g.ld()) {
                    var a = v(f);
                    D.vg(a, h, b, g, c, G / I);
                    rb._Shift(a - rb.Lc() - 1);
                    rb.M(a);
                    b.hf();
                    pc(a, d)
                }
            }
            function Z(b) {
                if (b == A && r == h && Ib() && !g.ld()) {
                    if (!i) {
                        var a = e;
                        if (D)
                            if (D.ac == h)
                                a = D.Sg();
                            else
                                D.Zb();
                        i = new ad(w,h,a,z);
                        i.eg(s)
                    }
                    !i._IsPlaying() && i.kd()
                }
            }
            function L(a, d, k) {
                if (a == h) {
                    if (a != d)
                        x[d] && x[d].Xe();
                    else
                        !k && i && i.gg();
                    s && s._Enable();
                    A = b.Ub();
                    g.Yb(b.Z(e, Z, A))
                } else {
                    var j = c.j(h, a)
                      , f = c.l(h, a)
                      , n = c.j(f - j, j + q - f)
                      , l = N + m._LazyLoading - 1;
                    (!J || n <= l) && g.Yb()
                }
            }
            function bb() {
                if (r == h && i) {
                    i.S();
                    s && s._Quit();
                    s && s._Disable();
                    i.Me()
                }
            }
            function fb() {
                r == h && i && i.S()
            }
            function Y(b) {
                !db && a.k(j._EVT_CLICK, h, b)
            }
            g.Ie = function(a) {
                if (S != a) {
                    S = a;
                    a && b.O(w, K);
                    !a && b.ib(K)
                }
            }
            ;
            g.Yb = function(f, c) {
                c = c || g;
                if (b.u(E) && !M) {
                    c.Ie(d);
                    if (!U) {
                        U = d;
                        a.k(j._EVT_LOAD_START, h);
                        b.f(E, function(a) {
                            var c = b.g(a, "data-load") || "src"
                              , d = !b.be(c, "data-") ? c.substring(5) : c;
                            if (!b.g(a, d)) {
                                var e = b.cb(a, d) || b.cb(a, "src2");
                                if (e) {
                                    b.g(a, d, e);
                                    b.vb(a, b.g(a, "data-display"))
                                }
                            }
                        })
                    }
                    b.Qh(E, o, b.Z(e, P, f, c))
                } else
                    P(f, c)
            }
            ;
            g.og = function() {
                if (Wc())
                    if (q == 1) {
                        g.Xe();
                        L(h, h)
                    } else {
                        var a;
                        if (D)
                            a = D.Mf(q);
                        if (a) {
                            A = b.Ub();
                            var c = h + eb
                              , d = x[v(c)];
                            return d.Yb(b.Z(e, W, c, d, a, A), g)
                        } else
                            (C || !sb(B.A()) || !sb(B.A() + eb)) && Wb(eb)
                    }
            }
            ;
            g.kc = function() {
                L(h, h, d)
            }
            ;
            g.Xe = function() {
                s && s._Quit();
                s && s._Disable();
                g.Ke();
                i && i.qg();
                i = e;
                Q()
            }
            ;
            g.hf = function() {
                b.vc(w)
            }
            ;
            g.Ke = function() {
                b.tb(w)
            }
            ;
            function T(a, h) {
                if (!h) {
                    u = b._FindChild(a, "bg");
                    y = u && b.Mb(u)
                }
                if (!b.g(a, Ob) && (h || !u)) {
                    var l = b.s(a, "data-arr");
                    if (l != f) {
                        function k(d, c) {
                            b.g(d, c, b.g(a, c))
                        }
                        var j = kb && kb._Transitions || {};
                        b.cf(a, b.g(a, "data-arr").endsWith(";"), b.hb(a), j[l], function(a, c) {
                            b.g(a, "data-t", b.u(j));
                            j.push(c);
                            k(a, "data-to");
                            k(a, "data-bf");
                            k(a, "data-c")
                        });
                        b.g(a, "data-arr", "")
                    }
                    var c = b.fb(a);
                    if (!u) {
                        y = a;
                        u = Dc(d);
                        b.g(u, "data-u", "bg");
                        var g = "background";
                        b.Q(u, g + "Color", b.Q(y, g + "Color"));
                        b.Q(u, g + "Image", b.Q(y, g + "Image"));
                        b.Q(y, g, e);
                        b.u(c) ? b.ub(u, c[0]) : b.O(y, u)
                    }
                    c = c.concat(b.oc(b.Ec(a, "data-tchd")));
                    b.f(c, function(c) {
                        if (h < 3 && !o)
                            if (b.cb(c, "u") == "image") {
                                o = c;
                                o.border = 0;
                                b.rb(o, ab);
                                b.rb(a, ab);
                                b.Q(o, "maxWidth", "10000px");
                                b.O(u, o)
                            }
                        T(c, h + 1)
                    });
                    if (h) {
                        b.g(a, "data-events", b.ic(a));
                        b.g(a, "data-display", b.vb(a));
                        !b.Vb(a) && b.Nh(a, b.g(a, "data-to"));
                        b.Sh(a, b.g(a, "data-bf"));
                        if (a.tagName == "IMG") {
                            E.push(a);
                            if (!b.g(a, "src")) {
                                J = d;
                                b.vc(a)
                            }
                        }
                        var i = b.g(a, "data-load");
                        i && E.push(a) && (J = J || !b.be(i, "data-"));
                        var m = i && b.g(a, i) || b.xf(a);
                        if (m) {
                            var n = new Image;
                            b.g(n, "data-src", m);
                            E.push(n)
                        }
                        b.N(a, (b.N(a) || 0) + 1)
                    }
                    b.Th(a, b.s(a, "data-p"));
                    b.Eh(a, b.cb(a, "po"));
                    b.jc(a, b.g(a, "data-ts"))
                }
            }
            g.md = function(c, b) {
                var a = N - b;
                Fc(V, a)
            }
            ;
            g.ac = h;
            T(w, 0);
            b.rb(w, ab);
            b.Bb(w, "hidden");
            b.jc(w, "flat");
            F = b.s(y, "data-fillmode", m._FillMode);
            var O = b._FindChild(y, "thumb", d);
            if (O) {
                g.ug = b.bb(O);
                b.vc(O)
            }
            b.tb(w);
            K = b.bb(R);
            b.N(K, 1e3);
            g.a(w, "click", Y);
            Q();
            g.Bg = o;
            g.Ne = w;
            g.Xc = V = w;
            g.a(a, 203, L);
            g.a(a, 28, fb);
            g.a(a, 24, bb);
            g._Destroy = function() {
                b._Destroy(hb, z, i)
            }
        }
        function ad(F, h, q, s) {
            var c = this, E = b.V(c, p), i = 0, u = 0, g, m, f, e, o, w, v, z = x[h];
            l.call(c, 0, 0);
            function B() {
                c.kd()
            }
            function C(a) {
                v = a;
                c.S();
                c.kd()
            }
            function A() {}
            c.kd = function() {
                if (!y && !fb && !v && r == h && !c.ld()) {
                    var k = c.A();
                    if (!k)
                        if (g && !o) {
                            o = d;
                            c.Me(d);
                            a.k(j._EVT_SLIDESHOW_START, h, u, i, u, g, e)
                        }
                    a.k(j._EVT_STATE_CHANGE, h, k, i, m, f, e);
                    if (!Jb()) {
                        var l;
                        if (k == e)
                            t && b._Delay(z.og, 20);
                        else {
                            if (k == f)
                                l = e;
                            else if (!k)
                                l = f;
                            else
                                l = c.Td();
                            (k != f || !Ic()) && c.xc(l, B)
                        }
                    }
                }
            }
            ;
            c.gg = function() {
                f == e && f == c.A() && c.M(m)
            }
            ;
            c.qg = function() {
                D && D.ac == h && D.Zb();
                var b = c.A();
                b < e && a.k(j._EVT_STATE_CHANGE, h, -b - 1, i, m, f, e)
            }
            ;
            c.Me = function(a) {
                q && b.Bb(T, a && q.ge._Outside ? "" : "hidden")
            }
            ;
            c.md = function(c, b) {
                if (o && b >= g) {
                    o = k;
                    z.Ke();
                    D.Zb();
                    a.k(j._EVT_SLIDESHOW_END, h, g, i, u, g, e)
                }
                a.k(j._EVT_PROGRESS_CHANGE, h, b, i, m, f, e)
            }
            ;
            c.eg = function(a) {
                if (a && !w) {
                    w = a;
                    a._On(JssorPlayer.sg, C)
                }
            }
            ;
            c.a(s, n.yc, A);
            q && c.vd(q);
            g = c.qb();
            c.vd(s);
            m = g + s.zc;
            e = c.qb();
            f = t ? g + s.cd : e;
            c._Destroy = function() {
                E._Destroy();
                c.S()
            }
        }
        function rc() {
            Nb = fb;
            hc = A.Td();
            bb = B.A();
            hb = Nc(bb)
        }
        function Kc() {
            rc();
            if (y || Jb()) {
                A.S();
                a.k(j.Yf)
            }
        }
        function Jc(f) {
            if (Ib()) {
                var b = B.A()
                  , a = hb
                  , e = 0;
                if (f && c.P(L) >= m._MinDragOffsetToSlide) {
                    a = b;
                    e = Db
                }
                a = c.H(a);
                a = xb(a + e, .5);
                var d = c.P(a - b);
                if (d < 1 && m._SlideEasing != g._Linear)
                    d = c.z(d, .5);
                if ((!db || !f) && Nb)
                    A.xc(hc);
                else if (b == a)
                    tc.kc();
                else
                    A.Re(b, a, d * Gb)
            }
        }
        function gc(a) {
            !b.gb(b._EvtSrc(a), f, o.Rd) && b._CancelEvent(a)
        }
        function Ac(b) {
            Fb = k;
            y = d;
            Kc();
            if (!Nb)
                z = 0;
            a.k(j._EVT_DRAG_START, v(bb), bb, b)
        }
        function Yc(a) {
            Ec(a, 1)
        }
        function Ec(c, d) {
            L = 0;
            Ab = 0;
            Db = 0;
            kc = sc;
            if (d) {
                var i = c.touches[0];
                Eb = {
                    x: i.clientX,
                    y: i.clientY
                }
            } else
                Eb = b.bd(c);
            var e = b._EvtSrc(c)
              , g = b.gb(e, "1", ic);
            if ((!g || g === u) && !W && (!d || b.u(c.touches) == 1)) {
                nb = b.gb(e, f, o.Rd) || !mb || !Zc();
                a.a(h, d ? "touchmove" : "mousemove", ac);
                Fb = !nb && b.gb(e, f, o.qd);
                !Fb && !nb && Ac(c, d)
            }
        }
        function ac(a) {
            var e, f;
            a = b.kh(a);
            if (a.type != "mousemove")
                if (b.u(a.touches) == 1) {
                    f = a.touches[0];
                    e = {
                        x: f.clientX,
                        y: f.clientY
                    }
                } else
                    gb();
            else
                e = b.bd(a);
            if (e) {
                var i = e.x - Eb.x
                  , j = e.y - Eb.y
                  , g = c.P(i)
                  , h = c.P(j);
                if (z || g > 1.5 || h > 1.5)
                    if (Fb)
                        Ac(a, f);
                    else {
                        if (c.G(hb) != hb)
                            z = z || S & W;
                        if ((i || j) && !z) {
                            if (W == 3)
                                if (h > g)
                                    z = 2;
                                else
                                    z = 1;
                            else
                                z = W;
                            if (Vb && z == 1 && h > g * 2.4)
                                nb = d
                        }
                        var l = i
                          , k = ob;
                        if (z == 2) {
                            l = j;
                            k = pb
                        }
                        (L - Ab) * qb < -1.5 && (Db = 0);
                        (L - Ab) * qb > 1.5 && (Db = -1);
                        Ab = L;
                        L = l;
                        mc = hb - L * qb / k / kc * m._DragRatio;
                        if (L && z && !nb) {
                            b._CancelEvent(a);
                            A.nf(d);
                            A.vf(mc)
                        }
                    }
            }
        }
        function gb() {
            Uc();
            a.W(h, "mousemove", ac);
            a.W(h, "touchmove", ac);
            db = L;
            if (y) {
                db && t & 8 && (t = 0);
                A.S();
                y = k;
                var b = B.A();
                a.k(j._EVT_DRAG_END, v(b), b, v(bb), bb);
                Y & 12 && rc();
                Jc(d)
            }
        }
        function Oc(c) {
            var e = b._EvtSrc(c)
              , a = b.gb(e, "1", Ob);
            if (u === a)
                if (db) {
                    b._StopEvent(c);
                    a = b.gb(e, f, "data-jssor-button", "A");
                    a && b._CancelEvent(c)
                } else {
                    t & 4 && (t = 0);
                    a = b.gb(e, f, "data-jssor-click");
                    if (a) {
                        b._CancelEvent(c);
                        hitValues = (b.g(a, "data-jssor-click") || "").split(":");
                        var g = b.Rh(hitValues[1]);
                        hitValues[0] == "to" && tb(g - 1);
                        hitValues[0] == "next" && tb(g, f, d)
                    }
                }
        }
        a._SlidesCount = function() {
            return q
        }
        ;
        a._CurrentIndex = function() {
            return r
        }
        ;
        a._CurrentPosition = function() {
            return B.A()
        }
        ;
        a._Idle = function(a) {
            if (a == f)
                return Kb;
            Kb = a
        }
        ;
        a._AutoPlay = function(a) {
            if (a == f)
                return t;
            if (a != t) {
                t = a;
                t && x[r] && x[r].kc()
            }
        }
        ;
        a._IsDragging = function() {
            return y
        }
        ;
        a._IsSliding = function() {
            return fb
        }
        ;
        a._IsMouseOver = function() {
            return !U
        }
        ;
        a._LastDragSucceeded = function() {
            return db
        }
        ;
        a._OriginalWidth = function() {
            return O
        }
        ;
        a._OriginalHeight = function() {
            return M
        }
        ;
        a._ScaleHeight = function(b) {
            if (b == f)
                return Cc || M;
            a._ScaleSize(b / M * O, b)
        }
        ;
        a._ScaleWidth = function(b) {
            if (b == f)
                return Zb || O;
            a._ScaleSize(b, b / O * M)
        }
        ;
        a._ScaleSize = function(c, a, d) {
            b.I(u, c);
            b.J(u, a);
            Hc = c / O;
            Bc = a / M;
            b.f(nc, function(a) {
                a._ScaleSize(Hc, Bc, d)
            });
            if (!Zb) {
                b.ub(T, w);
                b.T(T, 0);
                b.U(T, 0)
            }
            Zb = c;
            Cc = a
        }
        ;
        a.hh = yc;
        a.ih = wc;
        a._PlayTo = tb;
        a._GoTo = ub;
        a._Next = function() {
            Wb(1)
        }
        ;
        a._Prev = function() {
            Wb(-1)
        }
        ;
        a._Pause = function() {
            t = 0
        }
        ;
        a._Play = function() {
            a._AutoPlay(t || 1)
        }
        ;
        a._SetSlideshowTransitions = function(a) {
            m._SlideshowOptions._Transitions = a
        }
        ;
        a.Kd = function(a) {
            a = v(a);
            if (C & 1) {
                var d = F / E
                  , b = v(B.A())
                  , e = v(a - b + d)
                  , f = v(c.P(a - b));
                if (e >= N) {
                    if (f > q / 2)
                        if (a > b)
                            a -= q;
                        else
                            a += q
                } else if (a > b && e < d)
                    a -= q;
                else if (a < b && e > d)
                    a += q
            }
            return a
        }
        ;
        function Xc() {
            cc.Pe && b.Q(w, cc.Pe, ([e, "pan-y", "pan-x", "auto"])[mb] || "");
            a.a(u, "click", Oc, d);
            a.a(u, "mouseleave", Mc);
            a.a(u, "mouseenter", Lc);
            a.a(u, "mousedown", Ec);
            a.a(u, "touchstart", Yc);
            a.a(u, "dragstart", gc);
            a.a(u, "selectstart", gc);
            a.a(i, "mouseup", gb);
            a.a(h, "mouseup", gb);
            a.a(h, "touchend", gb);
            a.a(h, "touchcancel", gb);
            a.a(i, "blur", gb);
            m._ArrowKeyNavigation && a.a(h, "keydown", function(c) {
                if (!b.Ge(b._EvtSrc(c))) {
                    var a = b.Be(c);
                    if (a == 37 || a == 39) {
                        t & 8 && (t = 0);
                        fc(m._ArrowKeyNavigation * (a - 38) * qb, d)
                    }
                }
            })
        }
        function uc(d) {
            Gc.se();
            H = [];
            x = [];
            var e = b.fb(w)
              , g = b.Od(["DIV", "A", "LI"]);
            b.f(e, function(a) {
                var c = a;
                if (g[a.tagName.toUpperCase()] && !b.cb(a, "u") && b.vb(a) != "none") {
                    b.jc(a, "flat");
                    b.rb(a, ab);
                    H.push(a)
                }
                b.N(c, (b.N(c) || 0) + 1)
            });
            q = b.u(H);
            if (q) {
                var a = S & 1 ? zb : yb;
                b.rb(R, ab);
                F = m._Align;
                if (F == f)
                    F = (a - E + P) / 2;
                lb = a / E;
                N = c.j(q, m._Cols || q, c.H(lb));
                C = N < q ? m._Loop : 0;
                if (q * E - P <= a) {
                    lb = q - P / E;
                    F = (a - E + P) / 2;
                    Lb = (a - E * q + P) / 2
                }
                if (Cb) {
                    Qb = Cb._Class;
                    Sb = !F && N == 1 && q > 1 && Qb && (!b.Ue() || b.Zc() >= 9)
                }
                if (!(C & 1)) {
                    cb = F / E;
                    if (cb > q - 1) {
                        cb = q - 1;
                        F = cb * E
                    }
                    K = cb;
                    Q = K + q - lb - P / E
                }
                mb = (N > 1 || F ? S : -1) & m._DragOrientation;
                if (Sb)
                    D = new Qb(Bb,I,G,Cb,Vb,lc);
                b.f(H, function(a, b) {
                    x.push(new bd(a,b))
                });
                rb = new cd;
                B = new dd;
                A = new Tc(B,rb);
                Xc()
            }
            b.f(Z, function(a) {
                a.jd(q, x);
                d && a._On(s.Fc, fc)
            })
        }
        function Pb(a, d, g) {
            b.Ye(a) && (a = b.Xd("", a));
            var c, e;
            if (q) {
                if (d == f)
                    d = q;
                e = "beforebegin";
                c = H[d];
                if (!c) {
                    e = "afterend";
                    c = H[q - 1]
                }
            }
            b._Destroy(x);
            a && b.Mg(c || w, e || "afterbegin", a);
            b.f(g, function(a) {
                b.ib(a)
            });
            uc()
        }
        a._AppendSlides = function(e, a) {
            if (a == f)
                a = r + 1;
            var d = H[r];
            Pb(e, a);
            var c = 0;
            b.f(H, function(a, b) {
                a == d && (c = b)
            });
            ub(c)
        }
        ;
        a._ReloadSlides = function(a) {
            Pb(a, e, H);
            ub(0)
        }
        ;
        a._RemoveSlides = function(f) {
            var a = r
              , d = [];
            b.f(f, function(b) {
                if (b < q && b >= 0) {
                    d.push(H[b]);
                    b < r && a--
                }
            });
            Pb(e, e, d);
            a = c.j(a, q - 1);
            ub(a)
        }
        ;
        a.F = function(i, e) {
            a._Elmt = u = b._GetElement(i);
            O = b.I(u);
            M = b.J(u);
            m = b.B({
                _FillMode: 0,
                _LazyLoading: 1,
                _ArrowKeyNavigation: 1,
                _StartIndex: 0,
                _AutoPlay: 0,
                _Loop: 1,
                _HWA: d,
                _NaviQuitDrag: d,
                _AutoPlaySteps: 1,
                _Idle: 3e3,
                _PauseOnHover: 1,
                _SlideDuration: 500,
                _SlideEasing: g._OutQuad,
                _MinDragOffsetToSlide: 20,
                _DragRatio: 1,
                _SlideSpacing: 0,
                _UISearchMode: 1,
                _PlayOrientation: 1,
                _DragOrientation: 1
            }, e);
            m._HWA = m._HWA && b.th();
            if (m._DisplayPieces != f)
                m._Cols = m._DisplayPieces;
            if (m._ParkingPosition != f)
                m._Align = m._ParkingPosition;
            t = m._AutoPlay & 63;
            !m._UISearchMode;
            eb = m._AutoPlaySteps;
            Y = m._PauseOnHover;
            Y &= Vb ? 10 : 5;
            Kb = m._Idle;
            Gb = m._SlideDuration;
            S = m._PlayOrientation & 3;
            vb = b.Wg(b.g(u, "dir")) == "rtl";
            Hb = vb && (S == 1 || m._DragOrientation & 1);
            qb = Hb ? -1 : 1;
            Cb = m._SlideshowOptions;
            kb = b.B({
                _Class: n
            }, m._CaptionSliderOptions);
            jb = m._BulletNavigatorOptions;
            X = m._ArrowNavigatorOptions;
            J = m._ThumbnailNavigatorOptions;
            var c = b.fb(u);
            b.f(c, function(a, d) {
                var c = b.cb(a, "u");
                if (c == "loading")
                    R = a;
                else {
                    if (c == "slides") {
                        w = a;
                        b.Q(w, "margin", 0);
                        b.Q(w, "padding", 0);
                        b.jc(w, "flat")
                    }
                    if (c == "navigator")
                        Mb = a;
                    if (c == "arrowleft")
                        bc = a;
                    if (c == "arrowright")
                        Yb = a;
                    if (c == "thumbnavigator")
                        wb = a;
                    if (a.tagName != "STYLE" && a.tagName != "SCRIPT")
                        nc[c || d] = new Rc(a,c == "slides",b.Od(["slides", "thumbnavigator"])[c])
                }
            });
            R && b.ib(R);
            R = R || b.Lb(h);
            zb = b.I(w);
            yb = b.J(w);
            I = m._SlideWidth || zb;
            G = m._SlideHeight || yb;
            ab = {
                Cb: I,
                yb: G,
                _Top: 0,
                _Left: 0,
                ye: "block",
                Rb: "absolute"
            };
            P = m._SlideSpacing;
            ob = I + P;
            pb = G + P;
            E = S & 1 ? ob : pb;
            Bb = new Qc;
            b.g(u, Ob, "1");
            b.N(w, b.N(w) || 0);
            b.db(w, "absolute");
            T = b.bb(w, d);
            b.Q(T, "pointerEvents", "none");
            b.ub(T, w);
            b.O(T, Bb._Elmt);
            b.Bb(w, "hidden");
            if (Mb && jb) {
                jb.Jb = vb;
                vc = new jb._Class(Mb,jb,O,M);
                Z.push(vc)
            }
            if (X && bc && Yb) {
                X.Jb = vb;
                X._Loop = m._Loop;
                xc = new X._Class(bc,Yb,X,a);
                Z.push(xc)
            }
            if (wb && J) {
                J._StartIndex = m._StartIndex;
                J._ArrowKeyNavigation = J._ArrowKeyNavigation || 0;
                J.Jb = vb;
                oc = new J._Class(wb,J,R);
                !J._NoDrag && b.g(wb, ic, "1");
                Z.push(oc)
            }
            uc(d);
            a._ScaleSize(O, M);
            Ub();
            ub(m._StartIndex);
            b.Q(u, "visibility", "visible")
        }
        ;
        a._Destroy = function() {
            t = 0;
            b._Destroy(x, Z, Gc);
            b.Ob(u)
        }
        ;
        b.F(a)
    }
    ;
    j._EVT_CLICK = 21;
    j._EVT_DRAG_START = 22;
    j._EVT_DRAG_END = 23;
    j._EVT_SWIPE_START = 24;
    j._EVT_SWIPE_END = 25;
    j._EVT_LOAD_START = 26;
    j._EVT_LOAD_END = 27;
    j.Yf = 28;
    j._EVT_MOUSE_ENTER = 31;
    j._EVT_MOUSE_LEAVE = 32;
    j._EVT_POSITION_CHANGE = 202;
    j._EVT_PARK = 203;
    j._EVT_SLIDESHOW_START = 206;
    j._EVT_SLIDESHOW_END = 207;
    j._EVT_PROGRESS_CHANGE = 208;
    j._EVT_STATE_CHANGE = 209
}(window, document, Math, null, true, false);

var jssor_1_slider_init = function() {
    var jssor_1_SlideshowTransitions = [
      {_Duration:1200,_Zoom:1,_Easing:{_Zoom:_Jease._InCubic,_Opacity:_Jease._OutQuad},_Opacity:2},
      {_Duration:1000,_Zoom:11,_SlideOut:true,_Easing:{_Zoom:_Jease._InExpo,_Opacity:_Jease._Linear},_Opacity:2},
      {_Duration:1200,_Zoom:1,_Rotate:1,_During:{_Zoom:[0.2,0.8],_Rotate:[0.2,0.8]},_Easing:{_Zoom:_Jease._Swing,_Opacity:_Jease._Linear,_Rotate:_Jease._Swing},_Opacity:2,_Round:{_Rotate:0.5}},
      {_Duration:1000,_Zoom:11,_Rotate:1,_SlideOut:true,_Easing:{_Zoom:_Jease._InQuint,_Opacity:_Jease._Linear,_Rotate:_Jease._InQuint},_Opacity:2,_Round:{_Rotate:0.8}},
      {_Duration:1200,x:0.5,_Cols:2,_Zoom:1,_Assembly:2049,_ChessMode:{_Column:15},_Easing:{_Left:_Jease._InCubic,_Zoom:_Jease._InCubic,_Opacity:_Jease._Linear},_Opacity:2},
      {_Duration:1200,x:4,_Cols:2,_Zoom:11,_SlideOut:true,_Assembly:2049,_ChessMode:{_Column:15},_Easing:{_Left:_Jease._InExpo,_Zoom:_Jease._InExpo,_Opacity:_Jease._Linear},_Opacity:2},
      {_Duration:1200,x:0.6,_Zoom:1,_Rotate:1,_During:{_Left:[0.2,0.8],_Zoom:[0.2,0.8],_Rotate:[0.2,0.8]},_Opacity:2,_Round:{_Rotate:0.5}},
      {_Duration:1000,x:-4,_Zoom:11,_Rotate:1,_SlideOut:true,_Easing:{_Left:_Jease._InQuint,_Zoom:_Jease._InQuart,_Opacity:_Jease._Linear,_Rotate:_Jease._InQuint},_Opacity:2,_Round:{_Rotate:0.8}},
      {_Duration:1200,x:-0.6,_Zoom:1,_Rotate:1,_During:{_Left:[0.2,0.8],_Zoom:[0.2,0.8],_Rotate:[0.2,0.8]},_Opacity:2,_Round:{_Rotate:0.5}},
      {_Duration:1000,x:4,_Zoom:11,_Rotate:1,_SlideOut:true,_Easing:{_Left:_Jease._InQuint,_Zoom:_Jease._InQuart,_Opacity:_Jease._Linear,_Rotate:_Jease._InQuint},_Opacity:2,_Round:{_Rotate:0.8}},
      {_Duration:1200,x:0.5,y:0.3,_Cols:2,_Zoom:1,_Rotate:1,_Assembly:2049,_ChessMode:{_Column:15},_Easing:{_Left:_Jease._InCubic,_Top:_Jease._InCubic,_Zoom:_Jease._InCubic,_Opacity:_Jease._OutQuad,_Rotate:_Jease._InCubic},_Opacity:2,_Round:{_Rotate:0.7}},
      {_Duration:1000,x:0.5,y:0.3,_Cols:2,_Zoom:1,_Rotate:1,_SlideOut:true,_Assembly:2049,_ChessMode:{_Column:15},_Easing:{_Left:_Jease._InExpo,_Top:_Jease._InExpo,_Zoom:_Jease._InExpo,_Opacity:_Jease._Linear,_Rotate:_Jease._InExpo},_Opacity:2,_Round:{_Rotate:0.7}},
      {_Duration:1200,x:-4,y:2,_Rows:2,_Zoom:11,_Rotate:1,_Assembly:2049,_ChessMode:{_Row:28},_Easing:{_Left:_Jease._InCubic,_Top:_Jease._InCubic,_Zoom:_Jease._InCubic,_Opacity:_Jease._OutQuad,_Rotate:_Jease._InCubic},_Opacity:2,_Round:{_Rotate:0.7}},
      {_Duration:1200,x:1,y:2,_Cols:2,_Zoom:11,_Rotate:1,_Assembly:2049,_ChessMode:{_Column:19},_Easing:{_Left:_Jease._InCubic,_Top:_Jease._InCubic,_Zoom:_Jease._InCubic,_Opacity:_Jease._OutQuad,_Rotate:_Jease._InCubic},_Opacity:2,_Round:{_Rotate:0.8}}
    ];

    var jssor_1_options = {
      _AutoPlay: 0,
      _SlideshowOptions: {
        _Class: _JssorSlideshowRunner,
        _Transitions: jssor_1_SlideshowTransitions,
        _TransitionsOrder: 1
      },
      _ArrowNavigatorOptions: {
        _Class: _JssorArrowNavigator
      },
      _ThumbnailNavigatorOptions: {
        _Class: _JssorThumbnailNavigator,
        _Rows: 1,
        _SpacingX: 14,
        _SpacingY: 12,
        _Orientation: 2,
        _Align: 156
      }
    };

    var jssor_1_slider = new _JssorSlider("jssor_1", jssor_1_options);

    /*#region responsive code begin*/

    var MAX_WIDTH = 960;

    function ScaleSlider() {
        var containerElement = jssor_1_slider._Elmt.parentNode;
        var containerWidth = containerElement.clientWidth;

        if (containerWidth) {

            var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

            jssor_1_slider._ScaleWidth(expectedWidth);
        }
        else {
            window.setTimeout(ScaleSlider, 30);
        }
    }

    ScaleSlider();

    _Jssor._AddEvent(window, "load", ScaleSlider);
    _Jssor._AddEvent(window, "resize", ScaleSlider);
    _Jssor._AddEvent(window, "orientationchange", ScaleSlider);
};

jQuery(document).ready(function ($) {
     jssor_1_slider_init();
});
</script>
    <!-- #endregion Jssor Slider End -->
</body>
</html>
