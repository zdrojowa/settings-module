<template>
    <div>
        <b-form @submit="save">
            <b-card header-tag="header">
                <template #header>
                    <div class="float-right">
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                    </div>
                </template>
                <b-card-text>
                    <b-row>
                        <b-col>
                            <b-form-group
                                label="Kłucz"
                                description="To pole musi być unikalne"
                            >
                                <b-form-input
                                    v-model="key"
                                    type="text"
                                    placeholder="Wpisz klucz"
                                    :state="keyState"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <b-form-group
                                label="Typ"
                                description="To pole jest wymagane"
                            >
                                <b-form-select v-model="type" :options="types"></b-form-select>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-form-checkbox switch
                            v-if="type === 'bool'"
                            v-model="value"
                        >
                            Wartość
                        </b-form-checkbox>
                        <div v-else-if="type === 'array'">
                            <label>Wartość</label>
                            <b-form-tags v-model="value"></b-form-tags>
                        </div>
                        <b-form-group
                            v-else-if="!isMedia"
                            label="Wartość"
                        >
                            <b-form-input
                                :type="inputType"
                                v-model="value"
                            ></b-form-input>
                            <b-form-input v-if="type === 'datetime'"
                                type="time"
                                v-model="time"
                            ></b-form-input>
                        </b-form-group>
                        <div v-else style="width:100%">
                            <selector
                                :extensions="extensions"
                                @media-selected="selectFile"
                                :route="mediaSearchRoute"
                                :media-route="mediaRoute"
                            ></selector>

                            <div class="grid">
                                <div v-for="(file ,i) in files" :key="i + file" class="column-flex">
                                    <file-view :file="file.file" :route="mediaRoute" :show="false" :type="file.type" :link="true"></file-view>
                                    <b-button type="button" @click="removeFile(i)" variant="danger">
                                        <b-icon-trash></b-icon-trash>
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </b-row>
                </b-card-text>
            </b-card>
        </b-form>
    </div>
</template>

<script>
    import Selector from './../../../../../../../vendor/zdrojowa/media-module/resources/assets/js/components/Selector'
    import FileView from './../../../../../../../vendor/zdrojowa/media-module/resources/assets/js/components/FileView'
    export default {
        props : ['csrf', 'route', 'setting', 'types', 'mediaSearchRoute', 'mediaRoute', 'checkKeyRoute'],
        components: {
            Selector,
            FileView
        },

        data() {
            return {
                key: '',
                value: '',
                time: '',
                type: '',
                keyState: null,
                files: [],
                date: '',
                extensions: ''
            }
        },

        created() {
            if (this.setting) {
                this.key   = this.setting.key
                this.type  = this.setting.type
                this.value = this.setting.value
                if (this.isMedia) {
                    this.getFiles()
                }
                if (this.type === 'datetime') {
                    let date = this.value.split(' ')
                    this.value = date[0]
                    this.time  = date[1]
                }
            } else {
                this.type = 'string'
            }
        },

        computed: {
            isBool() {
                return this.type === 'bool'
            },
            isMedia() {
                return ['image', 'video', 'file'].includes(this.type)
            },
            isImage() {
                return this.type === 'image'
            },
            isVideo() {
                return this.type === 'video'
            },
            isFile() {
                return this.type === 'file'
            },
            isString() {
                return !['bool', 'image', 'video', 'file', 'date', 'datetime', 'array'].includes(this.type)
            },
            isDate() {
                return ['date', 'datetime'].includes(this.type)
            },
            isArray() {
                return this.type === 'array'
            },
            inputType() {
                let type = 'text'
                if (this.isDate) {
                    type = 'date'
                }
                return type
            }
        },
        methods: {
            getFiles() {
                let self = this
                this.value.forEach(file => {
                    axios.get(self.mediaSearchRoute + '?search=' + file.id)
                    .then(res => {
                        self.files.push({file: res.data[0], type: file.type})
                    }).catch(err => {
                        console.log(err)
                    })
                })
            },

            selectFile(file) {
                this.files.push(file)
            },

            removeFile(position) {
                this.files.splice(position, 1)
            },

            save(e) {
                e.preventDefault()
                if (this.keyState) {
                    let formData = new FormData()
                    formData.append('_method', this.setting ? 'PUT' : 'POST')
                    formData.append('key', this.key)
                    formData.append('type', this.type)
                    formData.append('value', this.value)
                    formData.append('array', JSON.stringify(this.value))
                    formData.append('time', this.time)
                    formData.append('files', JSON.stringify(this.files))

                    axios.post(this.route, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': this.csrf
                        }
                    })
                    .then(res => {
                        window.location = res.data.redirect
                    }).catch(err => {
                        console.log(err);
                    });
                } else {
                    return false;
                }
            },

            checkKeyUnique() {
                let route = this.checkKeyRoute + '?key=' + this.key
                if (this.setting) {
                    route += '&id=' + this.setting.id
                }
                axios.get(route)
                .then(res => {
                    this.keyState = !!res.data
                }).catch(err => {
                    console.log(err)
                })
            }
        },

        watch: {
            key() {
                if (!this.key.length) {
                    this.keyState = false
                } else {
                    this.checkKeyUnique()
                }
            },

            type(val, oldVal) {
                if (oldVal !== '') {
                    if (this.type === 'bool') {
                        this.value = false
                    }
                    if (this.type === 'string') {
                        this.value = ''
                    }
                    if (['int', 'decimal'].includes(this.type)) {
                        this.value = this.value >> 0
                    }
                    if (['date', 'datetime'].includes(this.type)) {
                        this.value = ''
                        this.time = ''
                    }
                    if (this.type === 'array') {
                        this.value = []
                    }
                    if (['image', 'video', 'file'].includes(oldVal)) {
                        this.files = []
                    }

                    if (this.type === 'file') {
                        this.extensions = ''
                    }
                    if (this.type === 'video') {
                        this.extensions = '3gp,3g2,asf,wmv,avi,divx,evo,f4v,flv,mp4,mpg,mpeg'
                    }
                    if (this.type === 'image') {
                        this.extensions = 'jpg,jpeg,png,svg'
                    }
                }
            },
        }
    }
</script>
