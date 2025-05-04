import React, { createContext, useReducer } from "react";

// Contexto para compartir estado y dispatch
export const CartContext = createContext();

// Estado inicial con productos y carrito vacío
const initialState = {
  products: [
    { id: 1, name: "Zapatos", price: 50 },
    { id: 2, name: "Camiseta", price: 20 },
    { id: 3, name: "Pantalones", price: 40 },
  ],
  cart: [],
};

// Reducer para manejar acciones del carrito
function reducer(state, action) {
  switch (action.type) {
    case "ADD_TO_CART":
      // Verificar si el producto ya está en el carrito
      const inCart = state.cart.find(item => item.id === action.payload.id);
      if (inCart) {
        // Incrementar cantidad si ya existe
        return {
          ...state,
          cart: state.cart.map(item =>
            item.id === action.payload.id
              ? { ...item, quantity: item.quantity + 1 }
              : item
          ),
        };
      } else {
        // Añadir producto nuevo con cantidad 1
        return {
          ...state,
          cart: [...state.cart, { ...action.payload, quantity: 1 }],
        };
      }
    case "REMOVE_FROM_CART":
      // Eliminar producto del carrito
      return {
        ...state,
        cart: state.cart.filter(item => item.id !== action.payload.id),
      };
    default:
      return state;
  }
}

// Provider que envuelve la app para compartir estado
export const CartProvider = ({ children }) => {
  const [state, dispatch] = useReducer(reducer, initialState);

  return (
    <CartContext.Provider value={{ state, dispatch }}>
      {children}
    </CartContext.Provider>
  );
};
