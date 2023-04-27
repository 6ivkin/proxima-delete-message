import React, { useState } from 'react'

const Counter = function () {
	const [count, setCount] = useState(0)

	function increment() {
		setCount(count + 1)
	}

	function decrement() {
		setCount(count - 1)
	}

	return (
		<div>
			<h1>{count}</h1>
			<button onClick={increment}>Увеличить</button>
			<button onClick={decrement}>Уменьшить</button>
		</div>
	)
}

export default Counter
