/* global jQuery, TRX_ADDONS_STORAGE */

if(TRX_ADDONS_STORAGE['web_push_appid'] !== ''){
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: TRX_ADDONS_STORAGE['web_push_appid'],
        });
    }); 
}