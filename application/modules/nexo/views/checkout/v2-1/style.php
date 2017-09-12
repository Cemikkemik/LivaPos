<style>
.calculator {
  padding: 10px;
  background-color: #ccc;
  border-radius: 5px;
  /*this is to remove space between divs that are inline-block*/
  font-size: 0;
}

.calculator > input[type=text] {
  width: 100%;
  height: 50px;
  border: none;
  background-color: #eee;
  text-align: right;
  font-size: 30px;
  padding-right: 10px;
}

.calculator .row { margin-top: 10px; }

.calculator .key {
    width: 63px;
    display: inline-block;
    background-color: white;
    color: #3e3e3e;
    font-size: 2rem;
    margin-right: 5px;
    border-radius: 5px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0px 1px 2px 0px #333;
}

.calculator .key:active {
    box-shadow:inset 0px 2px 2px 0px #333;
}

.calculator .key:hover { cursor: pointer; }

.key.last { margin-right: 0px; }

.key.action { background-color: #f9f7c5; }
</style>