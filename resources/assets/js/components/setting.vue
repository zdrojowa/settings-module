<template>
    <div class="card">

        <form method="POST" :action="url" @submit="validate" enctype="multipart/form-data">

            <slot></slot>

            <div class="card-header clearfix">
                <h4 v-if="_id" class="card-title float-left"><i class="mdi mdi-pencil"></i> Edytowanie ustawienia</h4>
                <h4 v-else class="card-title float-left"><i class="mdi mdi-plus"></i> Dodawanie nowego ustawienia</h4>
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </div>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Klucz</label>
                        <input type="text" :class="getInputClass('key')" name="key" placeholder="Wpisz klucz" v-model.lazy="obj.key">
                        <small v-if="hasError('key')" class="error mt-2 text-danger">{{ errors.key[0] }}</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Typ</label>
                        <multiselect v-model.lazy="obj.type" placeholder="Wybierz typ" :options="types"></multiselect>
                    </div>
                </div>

                <div v-if="!isFile" class="row">
                    <div class="form-group">
                        <label>Wartość</label>

                        <input v-if="isString" type="text" :class="getInputClass('value')" name="value" placeholder="Wpisz wartość" v-model="obj.value">

                        <input v-if="isBool" type="checkbox" :class="getInputClass('value')" name="value" v-model="obj.value">

                        <datetime v-if="isDate" v-model="obj.value" :type="obj.type" value-zone="local"></datetime>

                        <array v-if="isArray" :defaultItems="this.obj.array" @change="onArrayChange"/>

                        <small v-if="hasError('value')" class="error mt-2 text-danger">{{ errors.value[0] }}</small>
                    </div>
                </div>

                <div v-if="isVideo" class="row">
                    <b-form-group label="Video">
                        <media-selector extensions="3gp,3g2,asf,wmv,avi,divx,evo,f4v,flv,mp4,mpg,mpeg" @media-selected="selectFile" key="video"></media-selector>
                    </b-form-group>

                    <b-container fluid class="p-4 bg-dark d-flex">
                        <div v-for="(file ,i) in files" :key="i + file" class="thumbnail-video">
                            <b-embed type="video" aspect="4by3" controls poster="poster.png">
                                <source :src="file" type="video/webm">
                                <source :src="file" type="video/mp4">
                            </b-embed>
                            <b-button type="button" @click="removeFile(i)" variant="danger">
                                <b-icon-trash></b-icon-trash>
                            </b-button>
                        </div>
                    </b-container>
                </div>

                <div v-if="isImage" class="row">
                    <b-form-group label="Images">
                        <media-selector extensions="jpg,jpeg,png,svg" @media-selected="selectFile" key="image"></media-selector>
                    </b-form-group>

                    <b-container fluid class="p-4 bg-dark d-flex">
                        <div v-for="(file ,i) in files" :key="i + file" class="thumbnail-img">
                            <b-img thumbnail fluid :src="imageSrc(file)"></b-img>
                            <b-button type="button" @click="removeFile(i)" variant="danger">
                                <b-icon-trash></b-icon-trash>
                            </b-button>
                        </div>
                    </b-container>
                </div>

                <div v-if="isFileType" class="row">
                    <b-form-group label="Pliki">
                        <media-selector extensions="all" @media-selected="selectFile" key="file"></media-selector>
                    </b-form-group>

                    <b-container fluid class="p-4 bg-dark d-flex">
                        <div v-for="(file ,i) in files" :key="i + file" class="thumbnail-img">
                            <a :href="file" target="_blank">
                                <b-icon-file-earmark></b-icon-file-earmark>
                            </a>
                            <b-button type="button" @click="removeFile(i)" variant="danger">
                                <b-icon-trash></b-icon-trash>
                            </b-button>
                        </div>
                    </b-container>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: "setting",
        props : ['_id'],
        data() {
            return {
                types: [],
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
        },

        computed: {
            file() {
                return this._id ? this.obj.value : '';
            },
            url() {
                return this._id ? ('/dashboard/settings/' + this._id) : '/dashboard/settings/store';
            },
            isBool() {
                return this.obj.type === 'bool';
            },
            isFile() {
                return ['image', 'video', 'file'].includes(this.obj.type);
            },
            isImage() {
                return this.obj.type === 'image';
            },
            isVideo() {
                return this.obj.type === 'video';
            },
            isFileType() {
                return this.obj.type === 'file';
            },
            isString() {
                return !['bool', 'image', 'video', 'file', 'date', 'datetime', 'array'].includes(this.obj.type);
            },
            isDate() {
                return ['date', 'datetime'].includes(this.obj.type);
            },
            isArray() {
                return this.obj.type === 'array';
            }
        },
        methods: {
            imageSrc(file) {
                let src   = file;
                let parts = file.split('-');
                let name  = parts[parts.length - 1];
                if (name !== 'dashboardthumbnail') {
                    src += '-dashboardthumbnail';
                }
                return src;
            },

            selectFile(file) {
                this.files.push(file);
            },

            fileId(url) {
                let parts = url.split('/');
                let name  = parts[parts.length - 1];
                let index = name.indexOf('-');
                if (index !== -1) {
                    name = name.substr(0, index);
                }
                return name;
            },

            removeFile(position) {
                this.files.splice(position, 1);
            },

            onArrayChange(items) {
                this.obj.array = items;
            },

            validate(e) {
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

            hasError(key) {
                return this.errors[key].length > 0;
            },

            getInputClass(key) {
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

            getSetting() {
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

            getTypes() {
                axios.get('/dashboard/settings/types')
                    .then(res => {
                        this.types = res.data;

                        this.getSetting();
                    }).catch(err => {
                    console.log(err)
                })
            },

            checkKeyUnique() {
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
            'obj.key'() {
                if (!this.obj.key) {
                    this.errors.key = ['To pole jest wymagane'];
                } else {
                    this.checkKeyUnique();
                }
            },

            'obj.type'(val, oldVal) {
                if (this.obj.type === 'int') {
                    this.obj.value = this.obj.value >> 0;
                }
                if (this.obj.type === 'decimal') {
                    this.obj.value = this.obj.value.replace(/[^\d.-]/g, '');
                }
                if (['date', 'datetime'].includes(this.obj.type)) {
                    this.obj.value = '';
                }
                if (['image', 'video', 'file'].includes(oldVal)) {
                    this.files = [];
                }
            },
        }
    }
</script>
