<template>
    <div class="container">
        <div class="app">
            <div class="app__header">
                <h2 id="time-header" v-if="!ended">Время игры: <span id="time">{{ time }}</span></h2>
                <h2 id="result-header" v-else>Ваш результат: {{ score }}<span id="result"></span></h2>
            </div>
            <div class="app__content">
                <button class="btn" id="start" v-if="!isGameStarted" @click="startGame">Начать</button>

                <div class="game" id="game"></div>
            </div>
            <div class="app__footer">
                <div class="input">
                    <label for="game-time">Время игры, (сек)</label>
                    <input type="number" id="game-time" min="5" step="1"  :disabled="isGameStarted" v-model="gameTime">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                time: 5,
                gameTime: 5,
                score: 0,
                colors: ['red', 'blue', 'green', 'yellow', 'pink'],
                isGameStarted: false,
                ended: false
            }
        },
        watch: {
            gameTime() {
                this.time = this.gameTime;
                this.ended = false
            }
        },
        methods: {
            setTimer() {
                var interval = setInterval(() => {
                    if(this.time > 0) {
                        this.time = (this.time - 0.1).toFixed(1)
                    } else {
                        clearInterval(interval);
                        this.endGame();
                    }
                }, 100);
            },
            startGame() {
                this.score = 0;
                this.ended = false;
                var $game = document.querySelector('#game');
                $game.style.backgroundColor = '#fff';
                $game.addEventListener('click', this.handleBoxClick);
                this.isGameStarted = true;
                this.setTimer();
                this.renderBox()
            },
            renderBox() {
                var $game = document.querySelector('#game');
                $game.innerHTML = '';
                var box = document.createElement('div');
                var boxSize = this.getRandom(30, 100);
                var gameSize = $game.getBoundingClientRect();
                var maxTop = gameSize.height - boxSize;
                var maxLeft = gameSize.width - boxSize;
                var randomColorIndex = this.getRandom(0, this.colors.length);
                box.style.height = box.style.width = boxSize +'px';
                box.style.position = 'absolute';
                box.style.backgroundColor = this.colors[randomColorIndex];
                box.style.top = this.getRandom(0, maxTop) + 'px';
                box.style.left = this.getRandom(0, maxLeft) + 'px';
                box.style.cursor = 'pointer';
                box.setAttribute('data-box', 'true');

                $game.insertAdjacentElement('afterbegin' ,box);
            },
            getRandom(min, max) {
                return Math.floor(Math.random() * (max - min) + min)
            },
            handleBoxClick(event) {
                if(this.isGameStarted === true) {
                    if(event.target.dataset.box) {
                        this.renderBox();
                        this.score++
                    }
                }
            },
            endGame() {
                var $game = document.querySelector('#game');
                $game.innerHTML = '';
                this.isGameStarted = false;
                $game.innerHTML = '';
                $game.style.backgroundColor = '#ccc';
                this.time = this.gameTime;
                this.ended = true;
            }
        }
    }
</script>

<style scoped>
    @media screen and (max-device-width: 480px) {
        .app {
            width: auto !important;
        }
    }
    .app {
        background: #fff;
        padding: 20px;
        width: 600px;
        margin: 0 auto;
        box-shadow: 0 1px 30px 0 rgba(0,0,0,0.14), 0 0px 0px 0 rgba(0,0,0,.12), 0 0px 0px 0px rgba(0,0,0,.3);
        border-radius: 10px;
    }

    .app__header {
        padding: 20px;
        text-align: center;
    }

    .app__content {
        display: flex;
        position: relative;
        align-items: center;
        justify-content: center;
    }

    .app__footer {
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .input {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    .input label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 3px;
        color: #acacac;
        font-family: GothamPro,sans-serif;
        font-size: .8rem;
        font-weight: normal;
        line-height: 1.2rem;
    }

    .input input {
        display: block;
        width: 100%;
        max-height: 2.8rem;
        padding: .8rem 1rem;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: 1.4;
        color: #000;
        background: #fff none;
        border: 1px solid #999;
        border-radius: 5px;
    }

    .game {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ccc;
        background: #ccc;
        min-height: 300px;
        min-width: 300px;
        max-width: 300px;
        max-height: 300px;
    }

    .btn {
        position: absolute;
        z-index: 1;
        display: inline-block;
        padding: 1rem 2.3rem;
        margin-bottom: 0;
        font-weight: normal;
        font-family: GothamPro, sans-serif;
        font-size: 1rem;
        line-height: 1.3rem;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        user-select: none;
        background: #fad64e;
        color: #3f3f3f;
        border: none;
        border-radius: 2.8rem;
        transition: transform .2s ease-in-out,box-shadow .2s ease-in-out,-webkit-transform .2s ease-in-out,-webkit-box-shadow .2s ease-in-out;
        will-change: transform;
    }

    .btn:disabled {
        background: #ccc!important;
        border: 1px solid #ccc;
        color: #000;
        cursor: not-allowed;
    }

    .btn:active {
        transition: transform .1s ease-in-out,box-shadow .1s ease-in-out!important;
        transform: none!important;
        box-shadow: none!important;
    }

    .btn:hover {
        box-shadow: 0 6px 16px 0 rgba(0,0,0,.2);
        transform: translateY(-1px);
    }
</style>
