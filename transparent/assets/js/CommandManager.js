class CommandManager {
  constructor() {
    this.undoStack = [];
    this.redoStack = [];
  }
  executeCommand(cmd, func, oldValue, newValue) {
    console.log('executeCommand', func, oldValue, newValue)
    this.undoStack.push({cmd, func, oldValue, newValue});
    // Clear redo stack whenever a new command is executed
    this.redoStack = [];
  }
  undo() {
    if (this.undoStack.length) {
      const {cmd, func, oldValue, newValue} = this.undoStack.pop();
      console.log('Undoing last command', func, oldValue, newValue);
      cmd[func](oldValue);
      this.redoStack.push({cmd, func, oldValue: newValue, newValue: oldValue}); // Store the command for redo
    }
  }
  redo() {
    if (this.redoStack.length) {
      const {cmd, func, oldValue, newValue} = this.redoStack.pop();
      console.log('Redoing last command', func, oldValue, newValue);
      cmd[func](oldValue);
      this.undoStack.push({cmd, func, oldValue: newValue, newValue: oldValue});
    }
  }
}
