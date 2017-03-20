@extends('layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')

    <section class="content-header">
        @include('partials.breadcrumbs')
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
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <vue-select :options="languagesList"
                                        v-model="selectedLocale"
                                        @input="getLangFile">
                                    </vue-select>
                                </div>
                                <div class="col-xs-6">
                                    <button @click="saveLangFile"
                                        v-if="langFileIsChanged"
                                        class="btn btn-success pull-right">
                                        {{ __('Save Configuration') }}
                                    </button>
                                </div>
                            </div>
                            <div class="row margin-top-md" v-if="selectedLocale">
                                <div class="col-xs-9">
                                    <div class="input-group">
                                        <input type="text"
                                            id="search-input"
                                            v-focus
                                            v-select-on-focus
                                            placeholder="{{ __('Search') }}"
                                            class="form-control"
                                            v-model="queryString"
                                            @keyup.enter="isNewKey ? addKeyToLangFile() : focusIt(null)">
                                        <i class="fa fa-search input-group-addon" @click="focusIt('search-input')"></i>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <button class="btn btn-success pull-right"
                                        v-if="isNewKey"
                                        @click="addKeyToLangFile">
                                        {{ __('Add Key') }}
                                    </button>
                                </div>
                            </div>
                            <div class="row" v-if="selectedLocale" :style="windowHeightCss">

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
                                            @keyup.enter="focusIt('search-input')"
                                            @input="langFileIsChanged = true">
                                        <span class="input-group-addon">
                                            <i class="btn btn-xs btn-danger fa fa-trash-o"
                                                @click="removeKey(key)"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button @click="saveLangFile"
                                    v-if="langFile.length"
                                    class="btn btn-primary"
                                    style="float:right">
                                    {{ __("Save Configuration") }}
                            </button>
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

        var vm = new Vue({

            el: '#app',
            data: {
                langFile: {},
                languagesList: JSON.parse('{!! $languaguesList !!}'),
                selectedLocale: null,
                queryString: null,
                langFileIsChanged: false,
                windowHeightCss: {
                    'max-height': $(window).height() - 370 + 'px',
                    'overflow-y': 'scroll'
                }
            },
            computed: {
                filteredLangFile: function() {

                    if (!this.queryString) {

                        return this.langFile;
                    }

                    var self = this,
                        langFile = JSON.parse(JSON.stringify(self.langFile)),
                        keys = Object.keys(self.langFile);

                    var matchingKeys = keys.filter(function(key) {

                        return key.toLowerCase().indexOf(self.queryString.toLowerCase()) > -1;
                    });

                    for (let key in langFile) {

                        if (matchingKeys.indexOf(key) === -1) {

                            delete langFile[key];
                        }
                    }

                    return langFile;
                },
                isNewKey: function() {

                    return this.queryString && Object.keys(this.filteredLangFile).indexOf(this.queryString) === -1;
                },
            },
            methods: {
                getLangFile: function() {

                    axios.get('/system/localisation/getLangFile/' + this.selectedLocale).then((response) => {

                        this.langFile = response.data;
                    }).catch((error) => {

                        toastr[error.data.level](error.data.message);
                    });
                },
                saveLangFile: function() {

                    axios.patch('/system/localisation/saveLangFile', {

                        langFile: this.langFile,
                        locale: this.selectedLocale
                    }).then((response) => {

                        toastr[response.data.level](response.data.message);
                        this.langFileIsChanged = false;
                    }).catch((error) => {

                        toastr[error.data.level](error.data.message);
                    });
                },
                addKeyToLangFile: function() {

                    var obj = {},
                        self = this;

                    obj[this.queryString] = null;
                    this.langFile = Object.assign({}, obj, this.langFile);
                    this.focusIt(self.queryString);
                    this.queryString = null;
                    this.langFileIsChanged = true;
                },
                removeKey: function(key) {

                    Vue.delete(this.langFile, key);
                },
                blurIt: function(id) {

                    var input = document.getElementById(id);
                        input.blur();
                },
                focusIt: function(id) {

                    if (!id) {

                        id = Object.keys(this.filteredLangFile)[0];
                    }

                    this.$nextTick(function() {

                        var input = document.getElementById(id);
                        input.focus();
                    });
                }
            }
        });
    </script>

@endpush