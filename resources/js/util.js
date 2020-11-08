/**
* クッキーの値を取得する
* @param {String} searchKey  検索するキー
* @returns {String} キーに対応する値
*/

export function getCookieValue(searchKey){
  if(typeof searchKey === 'undefined'){
    return '';

    let val = '';

    document.cookie.split(';').forEach(cookie => {
      const [key, value] = cookie.split('=');
      if(key === searchKey){
        return val = value;
      }
    });

    return val;
  }
}

const AUTHENTIC_STATUS = 401;
const VALIDATION_STATUS = 422;
const SERVER_STATUS = 500;
const AUTHENTIC_ERROR_MESSAGE = '認証エラーが発生いたしました。ログインし直して下さい。';
const VALIDATION_ERROR_MESSAGE = 'パラメータエラーが発生いたしました。';
const SERVER_ERROR_MESSAGE = 'サーバーエラーが発生いたしました。';
const UNEXPECTED_ERROR_MESSAGE = '予期せぬエラーが発生いたしました。';
const TO_LOGIN = '/login';

//グローバルメソッド

export default {
  methods: {
    handleErrors: function({e = null, router = null}){ //通常APIエラー処理
      var status = e.response.status;
      if(status == AUTHENTIC_STATUS){
        if(e.response.data.errors){
            alert(AUTHENTIC_ERROR_MESSAGE);
            if(!router){
              router.go(TO_LOGIN);
            }
        }else{
          if(e.response.data.message){ //セッション切れでツイッターIDが取得できないときに意図的にエラーをだす。
            alert(e.response.data.message);
            if(!router){
              router.go(TO_LOGIN);
            }
          }
        }
        return;
      }else if(status == VALIDATION_STATUS){
        alert(VALIDATION_ERROR_MESSAGE);
        if(!router){
          router.go(TO_LOGIN);
        }
        return;
      }else if(status == SERVER_STATUS){
        alert(SERVER_ERROR_MESSAGE);
        if(!router){
          router.go(TO_LOGIN);
        }
        return;
      }else{
        alert(UNEXPECTED_ERROR_MESSAGE);
        if(!router){
          router.go(TO_LOGIN);
        }
        return;
      }
    }
  }
}
