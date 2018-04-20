// for Each Node list : loop through NodeList
export default function (array, callback, scope) {
  for (let i = 0; i < array.length; i++) {
    callback.call(scope, array[i], i) // https://toddmotto.com/ditch-the-array-foreach-call-nodelist-hack/
  }
}
