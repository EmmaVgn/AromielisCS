import './bootstrap.js';
import './styles/app.css';
import './styles/carousel.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import Filter from './js/Filter.js';



/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

new Filter(document.querySelector('.js-filter'))

const priceSlider = document.getElementById('price-slider');

if (priceSlider) {
    const min = document.getElementById('minPrice');
    const max = document.getElementById('maxPrice');
    const minValue = Math.floor(parseInt(priceSlider.dataset.min, 10) / 100);
    const maxValue = Math.ceil(parseInt(priceSlider.dataset.max, 10) / 100);
    const range = noUiSlider.create(priceSlider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        step: 10,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });
    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle === 0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}