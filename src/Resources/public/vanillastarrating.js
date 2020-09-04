/**
 * This file is part of MetaModels/attribute_rating.
 *
 * (c) 2012-2020 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * The javascript is based on Lorenzo Stanco's MooStarRating
 *
 * @package    MetaModels/attribute_rating
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_rating/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

(function() {
    var local = {};
    var Storage = {};

    var createUUID = function() {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = '0123456789abcdef';
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = '4';  // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8]  = s[13] = s[18] = s[23] = '-';

        return s.join('');
    };

    local.getUIDHTML = function(node) {
        return node.uniqueNumber || (node.uniqueNumber = createUUID());
    };

    var Slick = local.Slick = (this.Slick || {});

    Slick.uidOf = function(node) {
        return local.getUIDHTML(node);
    };

    var get = function(uid) {
        return (storage[uid] || (storage[uid] = {}));
    };

    Element.implement('_store', function(key, value) {
        var uid = Slick.uidOf(this), s = Storage[uid] || (Storage[uid] = {});

        return (s[key] = value), this;
    });

    Element.implement('_retrieve', function(key, initial) {
        var uid = Slick.uidOf(this), s = Storage[uid] || (Storage[uid] = {}), undef = 'undefined';
        typeof initial !== undef && typeof s[key] === undef && (s[key] = initial);

        return s[key];
    });
}());

(function() {
    var VanillaStarRatingImages = {
        defaultImageFolder: '',
        defaultImageEmpty : 'star_empty.png',
        defaultImageFull  : 'star_full.png',
        defaultImageHover : null,
    };

    var VanillaStarRating = function() {
        this.options = {
            form         : null,
            radios       : 'rating',
            selector     : '',
            linksClass   : 'star',
            imageFolder  : VanillaStarRatingImages.defaultImageFolder,
            imageEmpty   : VanillaStarRatingImages.defaultImageEmpty,
            imageFull    : VanillaStarRatingImages.defaultImageFull,
            imageHover   : VanillaStarRatingImages.defaultImageHover,
            width        : 16,
            height       : 16,
            half         : false,
            tip          : null,
            tipTarget    : null,
            tipTargetType: 'text',
            disabled     : false,
            ajaxUrl      : '',
            requestToken : '',
            ajaxData     : '',
        };

        this.radios = [], this.stars = [], this.currentIndex = -1;
    };

    VanillaStarRating.prototype.initialize = function(source) {
        if (source === null || source === undefined) {
            source = {};
        }

        // Setup options
        this.options = Object.assign(this.options, source);

        // Fix image folder
        if ((this.options.imageFolder.length != 0) && (this.options.imageFolder.substr(-1) != '/')) {
            this.options.imageFolder += '/';
        }

        // Hover image as full if none specified
        if (this.options.imageHover == null) {
            this.options.imageHover = this.options.imageFull;
        }

        // Build radio selector
        var formQuery = this.options.form;
        this.options.form = document.getElementById(formQuery);
        if (!this.options.form) {
            this.options.form = document.querySelectorAll('form[name=' + formQuery + ']')[0];
        }

        var UID = Date.now();

        if (this.options.form) {
            var uniqueId = 'star_' + (UID++).toString(36);
            this.options.form.classList.add(uniqueId);
            this.options.selector += 'form.' + uniqueId + ' ';
        }
        this.options.selector += 'input[type=radio][name=' + this.options.radios + ']';

        // Loop elements
        var i = 0;
        var me = this;
        var lastElement = null;
        var count = document.querySelectorAll(this.options.selector).length;
        var width = parseInt(this.options.width);
        var widthOdd = width;
        var height = parseInt(this.options.height);
        if (this.options.half) {
            width = parseInt(width / 2);
            widthOdd = widthOdd - width;
        }

        var _c_items = document.querySelectorAll(this.options.selector);

        for (; i < _c_items.length;) {
            //Add item to radio list
            this.radios[i] = _c_items[i];
            if (_c_items[i].getAttribute('checked')) {
                this.currentIndex = i;
            }

            //If disabled, whole star rating control is disabled
            if (_c_items[i].getAttribute('disabled')) {
                this.options.disabled = true;
            }

            // Hide and replace
            _c_items[i].style.display = 'none';
            var _aElm = document.createElement('a');
            //_aElm.setAttribute('title', _c_items[i].getAttribute('title'));
            _aElm.classList.add(this.options.linksClass);
            this.stars[i] = _aElm;
            this.stars[i]._store('ratingIndex', i);

            this.stars[i].style.backgroundImage = 'url("' + this.options.imageFolder + this.options.imageEmpty + '")';
            this.stars[i].style.backgroundRepeat = 'no-repeat';
            this.stars[i].style.display = 'inline-block';
            this.stars[i].style.width = ((this.options.half && (i % 2)) ? widthOdd + 'px' : width + 'px');
            this.stars[i].style.height = height + 'px';


            if (this.options.half) {
                this.stars[i].style.backgroundPosition = ((i % 2) ? '-' + width + 'px 0' : '0 0');
            }

            this.stars[i].addEventListener('mouseenter', function() {
                me.starEnter(this._retrieve('ratingIndex'));
            });

            this.stars[i].addEventListener('mouseleave', function() {
                me.starLeave();
            });

            // Tip
            if (this.options.tip) {
                var title = this.options.tip;
                title = title.replace('[VALUE]', _c_items[i].value);
                title = title.replace('[COUNT]', count);
                if (this.options.tipTarget) {
                    this.stars[i]._store('ratingTip', title);
                } else {
                    this.stars[i].setAttribute('title', title);
                }
            }

            // Click event
            this.stars[i].addEventListener('click', function() {
                if (!me.options.disabled) {
                    me.setCurrentIndex(this._retrieve('ratingIndex'));
                    me.sendAjaxRequest(me.getValue());
                }
            });

            //Go on
            lastElement = _c_items[i];
            i++;

        }// end for loop

        var _cstars = this.stars;

        for (var i = 0; i < _cstars.length; i++) {
            lastElement.after(_cstars[i]);
            lastElement = _cstars[i];
        }

        // Enable / disable
        if (this.options.disabled) {
            this.disable();
        } else {
            this.enable();
        }

        // Fill stars
        this.fillStars();

        return this;
    };

    VanillaStarRating.prototype.setValue = function(value) {
        for (var i = 0; i < this.radios.length; i++) {
            if (this.radios[i].value == value) {
                this.currentIndex = i;
            }
        }
        this.refreshRadios();
        this.fillStars();

        return this;
    };

    VanillaStarRating.prototype.getValue = function() {
        if (!this.radios[this.currentIndex]) {
            return null;
        }

        return this.radios[this.currentIndex].value;
    };

    VanillaStarRating.prototype.setCurrentIndex = function(index) {
        this.currentIndex = index;
        this.refreshRadios();
        this.fillStars();

        return this;
    };

    VanillaStarRating.prototype.enable = function() {
        this.options.disabled = false;
        for (var i = 0; i < this.stars.length; i++) {
            this.stars[i].style.cursor = 'pointer';
        }
        this.refreshRadios();

        return this;
    };

    VanillaStarRating.prototype.disable = function() {
        this.options.disabled = true;
        for (var i = 0; i < this.stars.length; i++) {
            this.stars[i].style.cursor = 'default';
        }
        this.refreshRadios();

        return this;
    };

    VanillaStarRating.prototype.refresh = function() {
        this.fillStars();
        this.refreshRadios();

        return this;
    };

    VanillaStarRating.prototype.fillStars = function(hoverIndex) {
        for (var i = 0; i < this.stars.length; i++) {
            var image = this.options.imageEmpty;
            if (hoverIndex == null) {
                if (i <= this.currentIndex) {
                    image = this.options.imageFull;
                }
            }
            if (hoverIndex != null) {
                if (i <= hoverIndex) {
                    image = this.options.imageHover;
                }
            }
            this.stars[i].style.backgroundImage = 'url("' + this.options.imageFolder + image + '")';
        }

        return this;
    };

    VanillaStarRating.prototype.starEnter = function(index, _this) {
        if (this.options.disabled) {
            return;
        }

        this.fillStars(index);
        if (this.options.tip && this.options.tipTarget) {

            if (this.options.tipTargetType == 'text') {
                this.options.tipTarget.innerText = (this.stars[index])._retrieve('ratingTip');
            } else {
                this.options.tipTarget.innerHTML = (this.stars[index])._retrieve('ratingTip');
            }
        }

        return this;
    };

    VanillaStarRating.prototype.starLeave = function() {
        if (this.options.disabled) {
            return;
        }

        this.fillStars();
        if (this.options.tip && this.options.tipTarget) {
            if (this.options.tipTargetType == 'text') {
                this.options.tipTarget.innerText = '';
            } else {
                this.options.tipTarget.innerHTML = '';
            }
        }

        return this;
    };

    VanillaStarRating.prototype.setCurrentIndex = function(index) {
        this.currentIndex = index;
        this.refreshRadios();
        this.fillStars();

        return this;
    };

    VanillaStarRating.prototype.refreshRadios = function() {
        for (var i = 0; i < this.radios.length; i++) {
            this.radios[i].setAttribute('disabled', this.options.disabled);
            this.radios[i].setAttribute('checked', i == this.currentIndex);
        }

        return this;
    };

    VanillaStarRating.prototype.sendAjaxRequest = function(value) {
        var _radios = this.radios, _stars = this.stars, _currentIndex = this.currentIndex, _me = this;

        var data   = JSON.parse(this.options.ajaxData);
        var params = 'rating=' + value + '&data[id]=' + data.id + '&data[pid]=' + data.pid + '&data[item]=' + data.item
                     + '&REQUEST_TOKEN=' + this.options.requestToken;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.options.ajaxUrl);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(params);
        xhr.onload = function() {
            if (xhr.status === 200) {
                //success
                makeDisableRadios(_radios, _stars, _currentIndex, _me);
            } else {
                if (xhr.status !== 200) {
                    if (this.options.tip && this.options.tipTarget) {
                        if (this.options.tipTargetType == 'text') {
                            this.options.tipTarget.innerText = 'Error! ' + xhr.status;
                        } else {
                            this.options.tipTarget.innerHTML = 'Error! ' + xhr.status;
                        }
                    }
                }
            }
        };
        xhr.onerror = function() {
            if (this.options.tip && this.options.tipTarget) {
                if (this.options.tipTargetType == 'text') {
                    this.options.tipTarget.innerText = 'Error!';
                } else {
                    this.options.tipTarget.innerHTML = 'Error!';
                }
            }
        }
    };

    var makeDisableRadios = function(_radios, _stars, _curr_index, _me) {
        var disable = false;

        if (_radios == undefined || _radios == null) {
            return;
        }

        for (var i = 0; i < _radios.length; i++) {
            if (i == _curr_index) {
                _radios[i].setAttribute('checked', 'true');
                disable = true;
                break;
            }
        }

        if (disable) {
            for (var i = 0; i < _radios.length; i++) {
                _radios[i].setAttribute('disabled', 'true');
                _stars[i].style.cursor = 'default';
            }
        }

        _me.options.disabled = true;
    }

    // export to global namespace
    window.VanillaStarRating = function() {
        return new VanillaStarRating();
    };
})();
