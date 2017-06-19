@extends('laravel-enso/core::layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')

    <section class="content-header">
        @include('laravel-enso/menumanager::breadcrumbs')
    </section>
    <section class="content">
        <div class="row" v-cloak>
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">
                            {{ __("Edit Texts") }}
                        </div>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool btn-sm" data-widget="collapse">
                                <i class="fa fa-minus">
                                </i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-xs-6 col-md-3">
                                    <vue-select :options="locales"
                                        v-model="selectedLocale"
                                        @input="getLangFile()"
                                        placeholder="{{ __('Choose language') }}">
                                    </vue-select>
                                </div>
                                <div class="col-xs-6 col-md-3">
                                    <transition name="fade">
                                        <div class="margin-top-sm pull-right" v-if="keysCount">
                                            {{ __("You have a total of") }} <b>@{{ keysCount }}</b> {{ __('translations') }}
                                        </div>
                                    </transition>
                                </div>
                                <div class="col-xs-12 col-md-3 col-md-offset-3">
                                    <transition name="fade">
                                        <button @click="saveLangFile()"
                                            v-if="hasChanges"
                                            class="btn btn-success pull-right">
                                            {{ __('Save Configuration') }}
                                        </button>
                                    </transition>
                                </div>
                            </div>
                            <div class="col-xs-12 margin-top-md" v-if="selectedLocale">
                                <div class="col-xs-12 col-md-6">
                                    <div class="input-group">
                                        <input type="text"
                                            id="search-input"
                                            v-focus
                                            v-select-on-focus
                                            placeholder="{{ __('Search') }}"
                                            class="form-control"
                                            v-model="query"
                                            @keyup.enter="isNewKey ? addKey() : focusIt(null)">
                                        <i class="fa fa-search input-group-addon"></i>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-md-offset-3">
                                    <transition name="fade">
                                        <button class="btn btn-success pull-right"
                                            v-if="isNewKey"
                                            @click="addKey()">
                                            {{ __('Add Key') }}
                                        </button>
                                    </transition>
                                </div>
                            </div>
                            <div class="col-xs-12" v-if="selectedLocale" :style="windowHeightCss">
                                <div class="col-xs-3 text-center">
                                    <hr>
                                    <p style="font-size: 16px">
                                        {{ __("Key Name") }}
                                    </p>
                                </div>
                                <div class="col-xs-9 text-center">
                                    <hr>
                                    <p style="font-size: 16px">
                                        {{ __("Key Value") }}
                                    </p>
                                </div>
                                <div v-for="(value, key) in filteredLangFile" class="col-xs-12">
                                    <div class="col-xs-6 well well-sm">
                                        @{{ key }}
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="input-group">
                                            <input type="text"
                                                v-select-on-focus
                                                v-model="langFile[key]"
                                                :id="key"
                                                class="form-control"
                                                @keyup.enter="focusIt('search-input')">
                                            <span class="input-group-addon">
                                                <i class="btn btn-xs btn-danger fa fa-trash-o"
                                                    @click="removeKey(key)"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

@endsection

@push('scripts')

    <script>

        let vm = new Vue({
            el: '#app',

            data: {
                langFile: {},
                locales: {!! $locales !!},
                selectedLocale: null,
                query: null,
                hasChanges: false,
                windowHeightCss: {
                    'max-height': $(window).height() - 370 + 'px',
                    'overflow-y': 'scroll'
                }
            },

            computed: {
                filteredLangFile() {
                    if (!this.query) {
                        return this.langFile;
                    }

                    let self = this,
                        langFile = JSON.parse(JSON.stringify(self.langFile)),
                        keys = Object.keys(self.langFile);

                    let matchingKeys = keys.filter(function(key) {
                        return key.toLowerCase().indexOf(self.query.toLowerCase()) > -1;
                    });

                    for (let key in langFile) {
                        if (matchingKeys.indexOf(key) === -1) {
                            delete langFile[key];
                        }
                    }

                    return langFile;
                },
                isNewKey() {
                    return this.query && Object.keys(this.filteredLangFile).indexOf(this.query) === -1;
                },
                keysCount() {
                    return Object.keys(this.langFile).length;
                }
            },

            watch: {
                langFile: {
                    handler(newValue, oldValue) {
                        if (Object.keys(oldValue).length) {
                            this.hasChanges = true;
                        }
                    },
                    deep: true
                }
            },

            methods: {
                getLangFile() {
                    axios.get('/system/localisation/getLangFile/' + this.selectedLocale).then(response => {
                        this.langFile = response.data;
                    }).catch(error => {
                        this.reportEnsoException(error);
                    });
                },
                saveLangFile() {
                    axios.patch('/system/localisation/saveLangFile',
                        { langFile: this.langFile, locale: this.selectedLocale }
                    ).then(response => {
                        toastr.success(response.data.message);
                        this.hasChanges = false;
                    }).catch(error => {
                        this.reportEnsoException(error);
                    });
                },
                addKey() {
                    let obj = {},
                        self = this;
                    obj[this.query] = null;
                    this.$set($this.LangFile, this.query, null);
                    // this.langFile = Object.assign({}, obj, this.langFile);
                    this.query = null;
                    this.focusIt();
                },
                removeKey(key) {
                    this.$delete(this.langFile, key);
                },
                focusIt(id = null) {
                    id = id || Object.keys(this.filteredLangFile)[0];

                    this.$nextTick(function() {
                        document.getElementById(id).focus();
                    });
                }
            }
        });

    </script>

@endpush