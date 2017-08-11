@extends('laravel-enso/core::layouts.app')

@section('css')

    <style>

        div.edit-label-area {
            overflow-y: auto;
            box-shadow: inset 0px 5px 8px -5px rgba(0,0,0,0.3), inset 0px -5px 8px -5px rgba(0,0,0,0.5);
            padding: 8px;
        }

        div.read-only-label {
            font-style: italic;
        }

    </style>

@endsection

@section('pageTitle', __("Localisation"))

@section('content')

    <page v-cloak>
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
                        <div class="col-xs-12 margin-top-md margin-bottom-md" v-if="selectedLocale">
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
                        <div class="col-xs-12" v-if="selectedLocale" style="padding-top: 20px">
                            <div class="col-xs-12 edit-label-area" :style="windowHeightCss">
                                <div class="col-xs-3 text-center">
                                    <p style="font-size: 16px">
                                        {{ __("Key Name") }}
                                    </p>
                                </div>
                                <div class="col-xs-9 text-center">
                                    <p style="font-size: 16px">
                                        {{ __("Key Value") }}
                                    </p>
                                </div>
                                <div v-for="key in filteredKeys" class="col-xs-12 margin-bottom-md">
                                    <div class="col-xs-6 read-only-label">
                                        <input type="text" class="form-control" readonly :value="key">
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
    </page>

@endsection

@push('scripts')

    <script>

        const vm = new Vue({
            el: '#app',

            data: {
                langFile: {},
                locales: {!! $locales !!},
                selectedLocale: null,
                query: null,
                hasChanges: false,
                windowHeightCss: {
                    'max-height': $(window).height() - 370 + 'px',
                    'overflow-y': 'auto'
                }
            },

            computed: {
                langKeys() {
                    return Object.keys(this.langFile);
                },
                sortedKeys() {
                    return this.langKeys.sort((a,b) => {
                        if(a < b) return -1;
                        if(a > b) return 1;
                        return 0;
                    });
                },
                filteredKeys() {
                    if (!this.query) {
                        return this.sortedKeys;
                    }

                    let self = this;

                    return this.langKeys.filter(key => {
                        return key.toLowerCase().indexOf(self.query.toLowerCase()) > -1;
                    });
                },
                isNewKey() {
                    return this.query && this.filteredKeys.indexOf(this.query) === -1;
                },
                keysCount() {
                    return this.langKeys.length;
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
                    this.$set(this.langFile, this.query, null);
                    this.focusIt();
                },
                removeKey(key) {
                    this.$delete(this.langFile, key);
                },
                focusIt(id = null) {
                    id = id || this.query;

                    this.$nextTick(function() {
                        document.getElementById(id).focus();
                    });
                }
            }
        });

    </script>

@endpush