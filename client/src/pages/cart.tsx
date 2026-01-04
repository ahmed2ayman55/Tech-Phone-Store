import { Layout } from "@/components/layout";
import { useCart } from "@/hooks/use-cart";
import { Button } from "@/components/ui/button";
import { Link } from "wouter";
import { Minus, Plus, Trash2, ArrowRight } from "lucide-react";

export default function Cart() {
  const { items, updateQuantity, removeItem, total } = useCart();

  if (items.length === 0) {
    return (
      <Layout>
        <div className="container mx-auto px-4 py-24 text-center">
          <div className="w-24 h-24 bg-muted rounded-full flex items-center justify-center mx-auto mb-6">
            <Trash2 className="w-10 h-10 text-muted-foreground" />
          </div>
          <h2 className="text-3xl font-display font-bold mb-4">Your cart is empty</h2>
          <p className="text-muted-foreground mb-8">Looks like you haven't added anything yet.</p>
          <Link href="/products">
            <Button size="lg">Start Shopping</Button>
          </Link>
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="container mx-auto px-4 py-12">
        <h1 className="text-3xl font-display font-bold mb-8">Shopping Cart</h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          {/* Items List */}
          <div className="lg:col-span-2 space-y-6">
            {items.map((item) => (
              <div key={item.id} className="flex gap-6 p-6 bg-white border rounded-xl shadow-sm">
                <div className="w-24 h-24 bg-muted/20 rounded-lg flex-shrink-0 p-2">
                  <img src={item.imageUrl} alt={item.name} className="w-full h-full object-contain" />
                </div>
                
                <div className="flex-1 flex flex-col justify-between">
                  <div className="flex justify-between items-start">
                    <div>
                      <h3 className="font-bold text-lg">{item.name}</h3>
                      <p className="text-sm text-muted-foreground capitalize">{item.category}</p>
                    </div>
                    <div className="font-bold text-lg">${Number(item.price).toFixed(2)}</div>
                  </div>
                  
                  <div className="flex justify-between items-end">
                    <div className="flex items-center gap-3 bg-muted/30 rounded-lg p-1">
                      <button 
                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                        className="p-1 hover:bg-white rounded-md transition-colors"
                      >
                        <Minus className="w-4 h-4" />
                      </button>
                      <span className="text-sm font-medium w-4 text-center">{item.quantity}</span>
                      <button 
                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                        className="p-1 hover:bg-white rounded-md transition-colors"
                      >
                        <Plus className="w-4 h-4" />
                      </button>
                    </div>
                    
                    <button 
                      onClick={() => removeItem(item.id)}
                      className="text-sm text-destructive hover:underline flex items-center gap-1"
                    >
                      <Trash2 className="w-4 h-4" /> Remove
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Summary */}
          <div className="lg:col-span-1">
            <div className="bg-white border rounded-2xl p-6 shadow-sm sticky top-24">
              <h3 className="font-bold text-xl mb-6">Order Summary</h3>
              
              <div className="space-y-4 mb-6">
                <div className="flex justify-between text-muted-foreground">
                  <span>Subtotal</span>
                  <span>${total().toFixed(2)}</span>
                </div>
                <div className="flex justify-between text-muted-foreground">
                  <span>Shipping</span>
                  <span>Free</span>
                </div>
                <div className="border-t pt-4 flex justify-between font-bold text-lg">
                  <span>Total</span>
                  <span>${total().toFixed(2)}</span>
                </div>
              </div>

              <Link href="/checkout">
                <Button size="lg" className="w-full shadow-lg shadow-primary/20">
                  Proceed to Checkout <ArrowRight className="w-4 h-4 ml-2" />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
}
