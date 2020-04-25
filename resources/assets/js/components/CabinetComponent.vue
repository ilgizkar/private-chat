<template>
    <div class="container">
        <div class="alert alert-success" v-if="success">
            Данные успешно сохранены
        </div>
        <div class="row profile">
            <div class="col-md-3" style="margin-bottom: 25px;">
                <div class="profile-sidebar">
                    <div class="profile-userpic">
                        <img :src="src" class="img-responsive" alt="">
                    </div>
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{ name }}
                        </div>
                        <div class="profile-usertitle-job">
                            Новичок
                        </div>
                    </div>
                    <div class="profile-userbuttons">
                        <input type="file" accept="image/*" @change="sync" id="my_btn" style="display: none;">
                        <div>
                            <button type="button" class="btn add-ava btn-success btn-sm" @click.prevent="addAvatar">Изменить аватар</button>
                        </div>
                        <div>
                            <button type="button" class="btn add-ava btn-danger btn-sm" @click.prevent="removeProfile">Удалить профиль</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 offset-md-1">
                <div class="user-form">
                    <h4 style="margin-bottom: 25px;">Данные пользователя</h4>
                    <form>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Имя</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputEmal3" v-model="name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputEmail3" v-model="email">
                            </div>
                        </div>
                        <fieldset class="form-group">
                            <div class="row">
                                <legend class="col-form-label col-sm-2 pt-0">Пол</legend>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="1" v-model="gender" checked>
                                        <label class="form-check-label" for="gridRadios1">
                                            Женский
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" v-model="gender" value="0">
                                        <label class="form-check-label" for="gridRadios2">
                                            Мужской
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success float-right" @click.prevent="save">Сохранить данные</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                name: '',
                email: '',
                gender: '',
                success: false,
                image: '',
                content: '',
                manAva: 'https://bootdey.com/img/Content/avatar/avatar6.png',
                womanAva: 'https://www.bootdey.com/img/Content/avatar/avatar5.png',
                avatar: ''
            }
        },
        computed: {
            src () {
                if (this.content) {
                    return this.content;
                } else if(this.avatar) {
                    return this.avatar;
                } else {
                    if(this.gender == 0) {
                        return this.manAva;
                    } else {
                        return this.womanAva;
                    }
                }
            }
        },
        methods: {
            selectImage (file) {
                this.file = file;
                let reader = new FileReader();
                reader.onload = this.onImageLoad;
                reader.readAsDataURL(file);
            },
            sync (e) {
                e.preventDefault();
                this.image = e.target.files[0];
                this.selectImage(this.image);
                var data = new FormData();
                data.append('image', this.image);
                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    }
                };
                axios.post(`/cabinet/avatar`, data, config);
            },
            onImageLoad (e) {
                this.content = e.target.result;
                let filename = this.file instanceof File ? this.file.name : '';
                this.$emit('input', filename);
                this.$emit('image-changed', this.content);
            },
            save() {
                axios.post(`/cabinet/save`, {
                    name: this.name,
                    email: this.email,
                    gender: this.gender,
                }).then(res => {
                    this.success = true;
                    setTimeout(() => {
                        this.success = false
                    }, 5000);
                });
            },
            addAvatar() {
                var elem = document.querySelector('#my_btn');
                elem.click()
            },
            removeProfile() {
                axios.post(`/cabinet/remove`).then(res => {
                    window.location.reload();
                });
            }
        },
        created() {
            axios.post('/cabinet/show').then(res => {
                this.name = res.data.data.name;
                this.email = res.data.data.email;
                this.gender = res.data.data.gender;
                this.avatar = res.data.data.avatar ? '/storage/' + res.data.data.avatar : '';
            })
        }
    }
</script>

<style scoped>
    body {
        background: #F1F3FA;
    }

    @media screen and (max-device-width: 480px) {
        .profile-userpic img {
            margin: 0 30% !important;
        }
    }

    .add-ava {
        margin-bottom: 20px;
        width: inherit;
    }

    .user-form {
        padding: 30px;
        background: #fff;
    }
    /* Profile container */
    .profile {
        margin: 20px 0;
    }

    /* Profile sidebar */
    .profile-sidebar {
        padding: 20px 0 10px 0;
        background: #fff;
    }

    .profile-userpic img {
        float: none;
        margin: 0 25%;
        width: 120px;
        height: 120px;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        border-radius: 50% !important;
    }

    .profile-usertitle {
        text-align: center;
        margin-top: 20px;
    }

    .profile-usertitle-name {
        color: #5a7391;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .profile-usertitle-job {
        text-transform: uppercase;
        color: #5b9bd1;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .profile-userbuttons {
        text-align: center;
        margin-top: 10px;
    }

    .profile-userbuttons .btn {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 15px;
        margin-right: 5px;
    }

    .profile-userbuttons .btn:last-child {
        margin-right: 0px;
    }

    .profile-usermenu ul li {
        border-bottom: 1px solid #f0f4f7;
    }

    .profile-usermenu ul li:last-child {
        border-bottom: none;
    }

    .profile-usermenu ul li a {
        color: #93a3b5;
        font-size: 14px;
        font-weight: 400;
    }

    .profile-usermenu ul li a i {
        margin-right: 8px;
        font-size: 14px;
    }

    .profile-usermenu ul li a:hover {
        background-color: #fafcfd;
        color: #5b9bd1;
    }

    .profile-usermenu ul li.active {
        border-bottom: none;
    }

    .profile-usermenu ul li.active a {
        color: #5b9bd1;
        background-color: #f6f9fb;
        border-left: 2px solid #5b9bd1;
        margin-left: -2px;
    }
</style>
