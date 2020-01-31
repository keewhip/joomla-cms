/**
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
((document) => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    [].slice.call(document.querySelectorAll('input[type="password"]')).forEach((input) => {
      const toggleButton = input.parentNode.querySelector('.input-password-toggle');

      if (!toggleButton) {
        return;
      }

      toggleButton.addEventListener('click', () => {
        const icon = toggleButton.firstElementChild;
        const srText = toggleButton.lastElementChild;

        if (icon.classList.contains('icon-eye')) {
          // Update the icon class
          icon.classList.remove('icon-eye');
          icon.classList.add('icon-eye-close');

          // Update the input type
          input.type = 'text';

          // Update the text for screenreaders
          srText.innerText = Joomla.Text._('JHIDEPASSWORD');
        } else if (icon.classList.contains('icon-eye-close')) {
          // Update the icon class
          icon.classList.add('icon-eye');
          icon.classList.remove('icon-eye-close');

          // Update the input type
          input.type = 'password';

          // Update the text for screenreaders
          srText.innerText = Joomla.Text._('JSHOWPASSWORD');
        }
      });
    });
  });
})(document);
