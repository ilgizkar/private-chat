<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3  mb-20">
                <div class="card card-default">
                    <div class="card-header">Пользователи</div>
                        <ul class="list-group">
                            <li class="list-group-item" v-for="friend in friends" :key="friend.id">
                                <a href="" @click.prevent="openChat(friend)">
                                    {{ friend.name }}
                                    <span class="text-danger" v-if="friend.session && (friend.session.unreadCount > 0)">+{{ friend.session.unreadCount }}</span>
                                </a>
                                <i class="fa fa-circle mt5 float-right text-success" aria-hidden="true" v-if="friend.online"></i>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="col-md-9">
                <span v-for="friend in friends" :key="friend.id" v-if="friend.session">
                     <message-component
                         v-if="friend.session.open"
                         @close="close(friend)"
                         :friend=friend
                     ></message-component>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import MessageComponent from "./MessageComponent";
    export default {
        data() {
            return {
                friends: []
            }
        },
        methods: {
            close(friend) {
                friend.session.open = false
            },
            sendNotification(title, options) {
                if (!("Notification" in window)) {
                    alert('Ваш браузер не поддерживает HTML Notifications, его необходимо обновить.');
                } else if (Notification.permission === "granted") {
                    var notification = new Notification(title, options);
                    function clickFunc() {
                        window.open('https://ilgizkar.ru/home', '_blank');
                        this.close();
                    }
                    notification.onclick = clickFunc;
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission(function (permission) {
                        if (permission === "granted") {
                            var notification = new Notification(title, options);

                        } else {
                            alert('Вы запретили показывать уведомления');
                        }
                    });
                } else {
                    console.log(Notification.permission)
                }
            },
            audioNotyPlay() {
                var audio = new Audio(); // Создаём новый элемент Audio
                audio.src = 'https://audiokaif.ru/wp-content/uploads/2019/04/7-%D0%A1%D0%BA%D0%B0%D1%87%D0%B0%D1%82%D1%8C-%D0%B7%D0%B2%D1%83%D0%BA-%D0%BF%D1%80%D0%B8%D1%88%D0%BB%D0%BE-%D1%81%D0%BE%D0%BE%D0%B1%D1%89%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BD%D0%B0-%D1%8D%D0%BB%D0%B5%D0%BA%D1%82%D1%80%D0%BE%D0%BD%D0%BD%D1%83%D1%8E-%D0%BF%D0%BE%D1%87%D1%82%D1%83.mp3'; // Указываем путь к звуку "клика"
                audio.autoplay = true; // Автоматически запускаем
            },
            getFriends() {
                axios.post('/getFriends').then(res => {
                    this.friends = res.data.data;
                    this.friends.forEach(friend => {
                        if(friend.session) {
                            this.listenForEverySession(friend)
                        }
                    })
                })
            },
            listenForEverySession(friend) {
                Echo.private(`Chat.${friend.session.id}`).listen('PrivateChatEvent', e => {
                    if(friend.session.open) {
                        return ''
                    } else {
                        friend.session.unreadCount++;
                        console.log(friend.session);
                        this.audioNotyPlay();
                        this.sendNotification('Новое сообщение!', {
                            body: 'Кликните сюда для перехода к ilgizkar.ru',
                            icon: 'notification.png',
                            dir: 'auto'
                        });
                    }
                });
            },
            openChat(friend) {
                if(friend.session) {
                   this.closeAll();
                    friend.session.open = true;
                    friend.session.unreadCount = 0;
                } else {
                    this.createSession(friend);
                }
            },
            createSession(friend) {
                this.closeAll();
                axios.post('/session/create', {friend_id:friend.id})
                    .then(res => {(friend.session = res.data.data), (friend.session.open = true)});
            },
            closeAll() {
                this.friends.forEach(friend => {
                    if(friend.session) {
                        friend.session.open = false
                    }
                });
            }
        },
        created() {
            this.getFriends();
            Echo.channel('Chat').listen('SessionEvent', e => {
               let friend = this.friends.find(friend => friend.id == e.session_by);
               friend.session = e.session;
               this.listenForEverySession(friend);
            });
            Echo.join('Chat')
                .here((users) => {
                    this.friends.forEach(friend => {
                        users.forEach(user => {
                            if(user.id == friend.id){
                                friend.online = true
                            }
                         })
                     })
                })
                .joining((user) => {
                    this.friends.forEach(friend => {
                        friend.id == user.id ? friend.online = true : ''
                    })
                })
                .leaving((user) => {
                    this.friends.forEach(friend => {
                        friend.id == user.id ? friend.online = false :''
                    })
                });
        },
        components: {
            MessageComponent
        }
    }
</script>

<style scoped>
    .mb-20 {
        margin-bottom: 20px;
    }
    .mt5 {
        margin-top: 5px;
    }
</style>

