import firebase from "firebase/app";
import "firebase/auth";
import store from './store';

const MAX_SHOW_POPUP_WIDTH = 600;

const config = {
  apiKey: "AIzaSyDj-gESxnJE9R9iLfNwZRDXV6eB_g633Y8",
  authDomain: "twieve.firebaseapp.com",
  databaseURL: "https://twieve.firebaseio.com",
  projectId: "twieve",
  storageBucket: "twieve.appspot.com",
  messagingSenderId: "757104191968",
  appId: "1:757104191968:web:1f6ff8252527f995f7e385",
  measurementId: "G-ZGQTFHWXP1"
};


export default {
  init(){
    firebase.initializeApp(config);
    firebase.auth().setPersistence(firebase.auth.Auth.Persistence.SESSION);
  },
  logout(){
    firebase.auth().signOut();
  },
  login(){
    const provider = new firebase.auth.TwitterAuthProvider();
    if(window.innerWidth <= MAX_SHOW_POPUP_WIDTH){
      firebase.auth().signInWithRedirect(provider);
    }else{
      firebase.auth().signInWithPopup(provider);
    }
  }
};
