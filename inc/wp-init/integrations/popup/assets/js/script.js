/**
 * Swipecart Popup Integration Library for Woocommerce and Shopify
 * Version 2.0.0-2022.11.14
 * @author Manthan Kanani
 */
(function (global, factory) {
	typeof exports === "object" && typeof module !== "undefined" ? (module.exports = factory()) : typeof define === "function" && define.amd ? define(factory) : (global.Swipecart = factory());
})(this, function () {
	"use strict";
	/**
	 * Functions
	 * @author Manthan Kanani
	 * @since 1.0.0
	 **/
	function getURL() {
		let siteURL = "", scripts, scriptURL, request;
		try {
			siteURL = Shopify.shop;
		} catch {
			scripts = document.getElementsByTagName('script');
			scriptURL = scripts[scripts.length-1].src;
			request = new Request(scriptURL);
			siteURL = request.queryParams().shop;
		}
		return siteURL.replace(/(^\w+:|^)\/\//, "").replace(/\/$/, "");
	}
	/**
	 * Main Class to init
	 * @author Manthan Kanani
	 * @since 1.0.0
	 **/
	class Swipecart {
		constructor(obj) {
			this.showPopup();
		}
		showPopup() {
			var popup = new Popup();
			popup.showPopup();
		}
	}
	/**
	 * Cookie Class
	 * @author Manthan Kanani
	 * @since 1.0.0
	 **/
	class Cookie {
		all() {
			let cookie = document.cookie.split(";");
			cookie
				.map(function (m) {
					return m.replace(/^\s+/, "").replace(/\s+$/, "");
				})
				.forEach(function (c) {
					var arr = c.split("="),
						key = arr[0],
						value = null;
					var size = arr.length;
					if (size > 1) {
						value = arr.slice(1).join("");
					}
					cookie[key] = value;
				}, document.cookie);
			return cookie;
		}
		set(cname, cvalue, exdays) {
			const d = new Date();
			d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
			let expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}
		get(cname) {
			return this.all()[cname] || false;
		}
		destroy(cname) {
			this.set(cname, "", -1);
		}
		isset(cname) {
			return this.get(cname) ? true : false;
		}
	}
	/**
	 * Request Class
	 * @author Manthan Kanani
	 * @since 1.0.0
	 **/
	class Request {
		constructor(baseURL) {
			this.siteURL = baseURL;
		}
		queryParams(){
  			return JSON.parse('{"' + decodeURI(this.siteURL.split("?")[1]).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') +'"}');
		}
		fetchData(url, header, data, callback) {
			let params = {};
			params.method = header.method || "GET";
			params.cache = "force-cache";
			params.mode = "cors";
			if (header.method === "POST") {
				params.body = JSON.stringify(data);
			}
			try {
				fetch(this.siteURL + url, params)
					.then(async (response) => {
						var respData = await response.json();
						callback(respData);
					})
					.catch((error) => { });
			} catch (e) { }
		}
	}
	/**
	 * Popup Class
	 * @author Manthan Kanani
	 * @since 1.0.0
	 * @updated 2.5.0
	 **/
	class Popup {
		constructor() {
			this.siteURL = "https://rentechassets.s3.amazonaws.com/swipecart/";
			this.cookie = "swipecart-pu-hide";
			this.style = {
				type: 1,
				mobile_app_deep_link: null,
				html: { desktop: null, mobile: null },
				show: { desktop: true, mobile: true, timeout: 1 },
				data: { position: "bottom-left", positionD: "bottom-left", section: { img: { src: null }, title: { main: null, sub: "Download our mobile app" }, btn: { text: "GET", src: null } } },
			};
			this.position = {};
			this.defaultStyle();
		}
		defaultStyle(resp = {}) {
			try { this.style.type = resp.type; } catch { this.style.type = 1; }
			try { this.style.show.desktop = resp.show.desktop; } catch { this.style.show.desktop = true; }
			try { this.style.show.mobile = resp.show.mobile; } catch { this.style.show.mobile = true; }
			try { this.style.show.timeout = resp.show.timeout; } catch { this.style.show.timeout = 1; }
			try { this.style.data.position = resp.data.position; } catch { this.style.data.position = "bottom-left"; }
			try { this.style.data.section.img.src = resp.data.section.img.src; } catch { this.style.data.section.img.src = ""; }
			try { this.style.mobile_app_deep_link = resp.mobile_app_deep_link } catch { this.style.mobile_app_deep_link = null; }
			try { this.style.data.section.title.main = resp.data.section.title.main; } catch { this.style.data.section.title.main = ""; }
			try { this.style.data.section.title.sub = resp.data.section.title.sub; } catch { this.style.data.section.title.sub = "Download our mobile app"; }
			try { this.style.data.section.btn.text = resp.data.section.btn.text; } catch { this.style.data.section.btn.text = "GET"; }
			try { this.style.data.section.btn.src = resp.data.section.btn.src; } catch { this.style.data.section.btn.src = this.style.mobile_app_deep_link; }
			try { this.style.html.desktop = resp.html.desktop; } catch { this.style.html.desktop = null; }
			try { this.style.html.mobile = resp.html.mobile; } catch { this.style.html.mobile = null; }
			if (!this.style.data.section.btn.src) {
				this.style.data.section.btn.src = this.style.mobile_app_deep_link;
			}
		}
		isDevice(type) {
			let w = window.innerWidth;
			type = type || "desktop";
			if (w >= 991 && type == "desktop") return true;
			if (w < 991 && w > 767 && type == "tablet") return true;
			if (w <= 767 && type == "mobile") return true;
			return false;
		}
		showPopup() {
			let storeURL = getURL();
			let retriveURI = "banners/" + storeURL + ".json?timestamp=" + Date.now();
			let cookie = new Cookie();
			if (cookie.isset(this.cookie)) return false;
			let request = new Request(this.siteURL);
			request.fetchData(retriveURI, { "Content-Type": "application/json" }, {}, (resp) => {
				this.defaultStyle(resp);
				if ((this.style.show.desktop === false) && (this.style.show.mobile === false)) return false;
				if (!this.style.data.section.title.main || !this.style.data.section.img.src || !this.style.data.section.btn.src) return false;
				this.renderPopup();
			});
		}
		retrivePosition(value) {
			let [y, x] = value.split("-");
			x = this.isDevice("mobile") ? "center" : x;
			this.position.transform = "";
			if (y == "top") {
				this.position.top = "0";
			} else if (y == "bottom") {
				this.position.bottom = "0";
			} else if (y == "center") {
				this.position.top = "50%";
				this.position.transform += "translateY(-50%) ";
			}
			if (x == "left") {
				this.position.left = "0";
			} else if (x == "right") {
				this.position.right = "0";
			} else if (x == "center") {
				this.position.left = "50%";
				this.position.transform += "translateX(-50%)";
			}
		}
		renderPopup() {
			this.retrivePosition(this.style.data.position);
			let stylePosition = "";
			stylePosition += this.position.top ? "top:" + this.position.top + ";" : "";
			stylePosition += this.position.bottom ? "bottom:" + this.position.bottom + ";" : "";
			stylePosition += this.position.right ? "right:" + this.position.right + ";" : "";
			stylePosition += this.position.left ? "left:" + this.position.left + ";" : "";
			stylePosition += this.position.transform ? "transform:" + this.position.transform + ";" : "";
			let stylesheetmobile = `<style type="text/css">.swipecart-modal .sc-modal{position:fixed;margin:auto;opacity:0;visibility:hidden;width:max-content;z-index: 9999;${stylePosition}font-family:Arial,sans-serif}.swipecart-modal .btn-show:checked ~ .sc-modal{opacity:1;visibility:visible}.swipecart-modal .modal-body{transform:scale(0);transition: 0.2s all}.swipecart-modal .btn-show:checked ~ .sc-modal .modal-body{transform: scale(1)} @media(min-width:1200px){.swipecart-modal{display:none}} </style>`;
			let styledesktop = '<style type="text/css">.swipecart-modal-new{position:fixed;top:0;right:0;bottom:0;left:0;z-index:2147483647;border:none;pointer-events:none;font-family:Arial,sans-serif}.qr-code-infosection.qr-code-infosection-fix .qr-pop-up-iner{cursor:pointer;z-index:1}.qr-code-infosection.qr-code-infosection-fix{width:100%;height:100%}.qr-code-infosection .qr-pop-up-iner .qr-code-infosection-btn{box-shadow:0 4px 20px rgb(0 0 0 / 15%);border-radius:100px;padding:12px 16px;text-transform:uppercase;cursor:pointer;font-weight:700;font-size:14px;border:none;pointer-events:all;display:flex;align-items:center;line-height:24px;box-sizing:border-box;position:relative}.qr-code-infosection .qr-pop-up-iner .qr-code-infosection-btn img{width:24px;margin-right:8px;box-sizing:border-box}.qr-code-infosection.qr-code-infosection-fix .qr-pop-up-iner .qr-code-infosection-ctr{transform:translate3d(0,12px,0) scale3d(.98,.98,1) rotateY(0) rotateZ(0) skew(0deg,0deg);transform-style:preserve-3d;opacity:0;perspective:calc(480px * var(--scale));perspective-origin:50% 60%;transition:transform .3s;transform-style:preserve-3d;max-width:calc(240px * var(--scale));border-top:16px solid transparent;border-bottom:16px solid transparent}.qr-code-infosection.qr-code-infosection-fix .qr-pop-up-iner:hover .qr-code-infosection-ctr{transform:translate3d(0,0,0) scale3d(1,1,1) rotateZ(0) skew(0deg,0deg);transform-style:preserve-3d;opacity:1;pointer-events:all}.qr-code-infosection-ctr .ctr-qr-inr-dtls{display:flex;flex-direction:column;padding:calc(20px * var(--scale));border-radius:11px;text-decoration:none}.qr-code-infosection-ctr .ctr-qr-inr-dtls span{font-size:calc(22px * var(--scale));line-height:calc(28px * var(--scale));font-weight:700;text-transform:uppercase;text-align:left;word-break:break-word;font-family:Arial,sans-serif}.ctr-qr-inr-dtls #qr-code-img{background-color:#fff;text-align:center;margin-top:15px;padding:25px;display:flex;align-items:center;justify-content:center;}.ctr-qr-inr-dtls #qr-code-img svg{width:200px !important;height:200px !important}.cust-overlay-mobile-popup{transition:0.3s all}.ctr-qr-inr-dtls bdi{font-size:15px;}@media (min-width:1200px){.qr-code-infosection.qr-code-infosection-fix{--scale:1.25}} @media(max-width:1200px){.swipecart-modal-new{display:none}}</style>'
			let html = this.style.html.desktop;
			let htmlmobile = this.style.html.mobile;
			if (this.style.show.desktop === true && this.style.show.mobile === true) {
				var maindiv = document.createElement("div");
				maindiv.classList.add("swipecart-modal-new");
				maindiv.innerHTML = styledesktop + html;
				document.body.appendChild(maindiv);
				var maindivmobile = document.createElement("div");
				maindivmobile.innerHTML = (stylesheetmobile + htmlmobile)
				document.body.appendChild(maindivmobile);
			} else if (this.style.show.desktop === true) {
				var maindiv = document.createElement("div");
				maindiv.classList.add("swipecart-modal-new");
				maindiv.innerHTML = styledesktop + html;
				document.body.appendChild(maindiv);
			} else if (this.style.show.mobile === true) {
				var maindivmobile = document.createElement("div");
				maindivmobile.innerHTML = (stylesheetmobile + htmlmobile)
				document.body.appendChild(maindivmobile);
			}
			this.closePopup("modal-1-56-swipecart");
		}
		closePopup(name) {
			document.querySelector(".swipecart-modal .btn-hide[name=" + name + "]").addEventListener("change", (e) => {
				if (e.currentTarget.checked) {
					let cookie = new Cookie();
					cookie.set(this.cookie, "1", this.style.show.timeout);
				}
			});
		}
	}
	/**
	 * Initialize Whole Code
	 * @author Manthan Kanani
	 * @since 1.0.0
	 **/
	new Swipecart();
});