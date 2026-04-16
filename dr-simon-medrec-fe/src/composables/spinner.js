import { QSpinnerIos, Loading } from 'quasar';

export const useSpinner = () => {
  const show = (message = 'Loading. Please wait...') => {
    Loading.show({
      spinner: QSpinnerIos,
      spinnerColor: 'white',
      spinnerSize: 140,
      message,
      messageColor: 'white',
    });
  };

  const hide = () => Loading.hide();

  return { show, hide };
};