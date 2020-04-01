<template>
    <div class="card">
        <div class="card-header">
            <h4 v-if="_id" class="card-title">
                Edytowanie ustawienia
            </h4>
            <h4 v-else  class="card-title">
                Dodawanie nowego ustawienia
            </h4>
        </div>
        <div class="card-body">
            <form method="POST" :action="url" @submit="validate" enctype="multipart/form-data">

                <slot></slot>
                <input v-if="_id" type="hidden" name="_method" value="PUT">
                <div class="d-flex justify-content-center">
                    <div class="form-group col-6">
                        <label>Klucz</label>
                        <input type="text" :class="getInputClass('key')" name="key" placeholder="Wpisz klucz" v-model.lazy="obj.key">
                        <small v-if="hasError('key')" class="error mt-2 text-danger">{{ errors.key[0] }}</small>
                    </div>

                    <div class="form-group col-6">
                        <label>Typ</label>
                        <select type="text" class="form-control" name="type" id="type" v-model.lazy="obj.type" v-on:change="parseValue">
                            <option v-for="type in types" :value="type">{{ type }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wartość</label>

                    <input v-if="isString" type="text" :class="getInputClass('value')" name="value" placeholder="Wpisz wartość" v-model="obj.value">

                    <input v-if="isBool" type="checkbox" :class="getInputClass('value')" name="value" v-model="obj.value">

                    <datetime v-if="isDate" v-model="obj.value" :type="obj.type" value-zone="local"></datetime>

                    <button v-show="isFile" class="btn btn-success" id="media" type="button">Wybierz</button>

                    <array v-if="isArray" :defaultItems="this.obj.array" @change="onArrayChange"/>

                    <small v-if="hasError('value')" class="error mt-2 text-danger">{{ errors.value[0] }}</small>
                </div>

                <div v-if="isFile && files.length > 0" class="form-group">
                    <div class="dropzone-preview" style="text-align:center" :class="{ 'on': obj.value.length > 0 }">
                        <div v-for="(file ,i) in files" :key="i"  class="dropzone-img">
                            <button @click="removeFile(i)" class="btn-danger">
                              <i class="mdi mdi-delete"></i>
                            </button>
                            <a :href="file" target="_blank">
                                <img :src="file"/>
                            </a>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Zapisz</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "setting",
        props : ['_id'],
        data() {
            return {
                types: {},
                obj: {
                    _id: 0,
                    key: '',
                    value: '',
                    type: 'string',
                    array: []
                },
                errors: {
                    key: {},
                    type: {},
                    value: {}
                },
                files: [],
                date: ''
            }
        },

        created: function() {
            this.getTypes();
            this.getSetting();
        },

        mounted: function() {
            let self = this;
            $('#media').mediaSelector({
                extensions: 'all'
            });

            $("#media").on('media:selected', function(event, args) {
                self.selectFile(args);
            })
        },

        computed: {
            file: function() {
                return this._id ? this.obj.value : '';
            },
            url: function() {
                return this._id ? ('/dashboard/settings/' + this._id) : '/dashboard/settings/store';
            },
            isBool: function() {
                return this.obj.type === 'bool';
            },
            isFile: function() {
                return ['image', 'video', 'file'].includes(this.obj.type);
            },
            isImage: function() {
                return this.obj.type === 'image';
            },
            isString: function() {
                return !['bool', 'image', 'video', 'file', 'date', 'datetime', 'array'].includes(this.obj.type);
            },
            isDate: function() {
                return ['date', 'datetime'].includes(this.obj.type);
            },
            isArray: function() {
                return this.obj.type === 'array';
            }
        },
        methods: {
            selectFile: function(file) {
                this.files.push(file.url);
            },

            fileId: function(url) {
                let parts = url.split('/');
                let name  = parts[parts.length - 1];
                let index = name.indexOf('-');
                if (index !== -1) {
                    name = name.substr(0, index);
                }
                return name;
            },

            removeFile: function(position) {
                this.files.splice(position, 1);
            },

            onArrayChange: function(items) {
                this.obj.array = items;
            },

            validate: function(e) {
                e.preventDefault();
                if (this.obj.key && !this.hasError('key') && !this.hasError('value')) {
                    let formData = new FormData();
                    formData.append('obj', JSON.stringify(this.obj));
                    formData.append('_method', this._id ? 'PUT' : 'POST');
                    formData.append('files', JSON.stringify(this.files));

                    axios.post(this.url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(res => {
                            window.location = res.data.redirect;
                        }).catch(err => {
                        console.log(err);
                    });
                } else {
                    return false;
                }
            },

            hasError: function(key) {
                return this.errors[key].length > 0;
            },

            getInputClass: function(key) {
                let className = 'form-control ';
                if (this.hasError(key)) {
                    className += 'is-invalid';
                } else {
                    if (this.obj[key]) {
                        className += 'is-valid';
                    }
                }
                return className;
            },

            parseValue: function() {
                if (this.obj.type === 'int') {
                    this.obj.value = this.obj.value >> 0;
                }
                if (this.obj.type === 'decimal') {
                    this.obj.value = this.obj.value.replace(/[^\d.-]/g, '');
                }
                if (['date', 'datetime'].includes(this.obj.type)) {
                    this.obj.value = '';
                }
                this.files = [];
            },

            getSetting: function() {
                let self = this;
                if (self._id) {
                    axios.get('/dashboard/settings/get/' + self._id)
                        .then(res => {
                            self.obj = res.data;
                            if (self.isFile) {
                                self.files = JSON.parse(self.obj.value);
                            }
                            if (self.isArray) {
                                self.obj.array = JSON.parse(self.obj.value);
                            }
                        }).catch(err => {
                        console.log(err)
                    })
                }
            },

            getTypes: function() {
                axios.get('/dashboard/settings/types')
                    .then(res => {
                        this.types = res.data
                    }).catch(err => {
                    console.log(err)
                })
            },

            checkKeyUnique: function() {
                axios.get('/dashboard/settings/checkKey/' + this._id + '?key=' + this.obj.key)
                    .then(res => {
                        if (res.data) {
                            this.errors.key = [];
                        } else {
                            this.errors.key = ['To pole musi być unikalne'];
                        }
                    }).catch(err => {
                        console.log(err)
                })
            }
        },

        watch: {
            'obj.key': function() {
                if (!this.obj.key) {
                    this.errors.key = ['To pole jest wymagane'];
                } else {
                    this.checkKeyUnique();
                }
            }
        }
    }
</script>

<style>
    .el-icon-loading {font-size:24px;position:absolute;top:45%;left:45%;font-weight:bold;color:#5d56f9}
    .dropzone-area.full{position: absolute;background: transparent;border:none;top: 0;left: 0;right: 0;bottom: 0;margin: auto;height: 100%;width: 100%;}
    .dropzone-area.full .dropzone-message,.dropzone-area.full .dropzone-preview{opacity:0 !important;}
    .dropify{opacity: 0;position: absolute;height: 210px;cursor: pointer}
    .dropify-preview{height: 210px;background-color: #666;z-index: 15}
    .dropify:hover ~ .dropify-preview{background-color: #888;}
    .dropzone-area {display: block;position: relative;cursor: pointer;overflow: hidden;width: 100%;max-width: 100%;min-height: 200px;padding: 5px 10px;font-size: 14px;line-height: 22px;color: #585858;background-color: #FFF;background-image: none;text-align: center;border: 2px solid #cccccc;-webkit-transition: border-color .15s linear;transition: border-color .15s linear;border-radius: 4px;}
    .dropzone-area:hover{background-size: 30px 30px;background-image: -webkit-linear-gradient(135deg,#F6F6F6 25%,transparent 25%,transparent 50%,#F6F6F6 50%,#F6F6F6 75%,transparent 75%,transparent);background-image: linear-gradient(-45deg,#F6F6F6 25%,transparent 25%,transparent 50%,#F6F6F6 50%,#F6F6F6 75%,transparent 75%,transparent);-webkit-animation: stripes 2s linear infinite;animation: stripes 2s linear infinite;}
    .dropzone-area input {position: absolute;top: 0;right: 0;bottom: 0;left: 0;height: 100%;width: 100%;opacity: 0;cursor: pointer;z-index: 5;}
    .dropzone-info {font-size: 13px;font-size: 0.8125rem;color: #A8A8A8;letter-spacing: 0.4px;}
    .dropzone-button {position: absolute;top: 10px;right: 10px;display: none;}
    .dropzone-preview {height: 100%;}
    .dropzone-preview img {object-fit: contain;}
    .dropzone-img{width: 185px;height: 210px;display: inline-block;overflow: hidden;padding: 2px;position: relative;box-shadow: 0 0 5px 0px #aaa}
    .dropzone-img span{position: absolute;padding: 3px 8px;background: rgba(255,255,255,.5);right: 3px;top: 5px;border-radius: 3px;z-index: 999;width: 5px;border: 2px solid #ccc;opacity: .2;}
    .dropzone-img span:hover {transition: .3s all;background: #fff;width: auto; opacity: 1}
    .dropzone-img span.has-icon{position: absolute;padding: 0px 5px;background: hsla(0, 91.9%, 43.3%, 0.5);right: 3px;top: 5px;border-radius: 3px;z-index: 999;color:#fff;opacity: 1;width: auto;border: none;}
    .dropzone-img span.has-icon:hover{background: hsla(0, 91.9%, 43.3%, 0.73);}
    .dropzone-img img{width: 185px;height: 210px;object-fit: cover;}
    .dropzone-remove{position: absolute !important;right:2%;top:2%;opacity: 0.6;z-index: 5}
    .dropzone-remove:hover{opacity: 1}
    .dropzone-message{position: relative;top: 50%;-webkit-transform: translateY(-50%);transform: translateY(-50%);}
    .dropzone-message p {margin: 5px 0 0;color: #777;}
    .file-icon {font-size: 70px;color: #CCC;}
</style>
