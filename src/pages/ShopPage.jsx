import { Heart, Package, ShoppingBag, Star } from 'lucide-react'
import { useState } from 'react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { ImageWithFallback } from '../components/ImageWithFallback'
import { rescueProducts as products } from '../app/rescueStore'
import { api } from '../services/api'

function ShopPage() {
  const [status, setStatus] = useState('')

  const handleAddToCart = async (product) => {
    setStatus(`Adding ${product.name} to cart...`)
    try {
      await api.addToCart({ productId: product.id, quantity: 1 })
      setStatus(`${product.name} added to cart.`)
    } catch (error) {
      setStatus(`${error.message}. Backend endpoint pending at ${api.baseUrl}.`)
    }
  }

  return (
    <div className="min-h-screen bg-white px-4 py-12 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl">
        <section className="mb-10 overflow-hidden rounded-[38px] bg-[linear-gradient(120deg,_#17233c,_#26324d)] p-8 text-white shadow-[0_28px_70px_rgba(15,23,42,0.22)] lg:p-12">
          <div className="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-center">
            <div>
              <div className="mb-4 inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-100">
                Rescue shop
              </div>
              <h1 className="max-w-3xl text-5xl font-black leading-none tracking-tight">
                Every purchase supports food, care, and emergency rescue.
              </h1>
              <p className="mt-5 max-w-2xl text-lg leading-8 text-slate-300">
                A frontend-ready shop page for donations, rescue supplies, and future payment integration.
              </p>
            </div>
            <div className="rounded-[30px] border border-white/10 bg-white/8 p-6">
              <Heart className="mb-5 h-10 w-10 fill-current text-orange-400" />
              <div className="text-4xl font-black">$24,860</div>
              <p className="mt-2 text-sm font-semibold text-slate-300">Raised for shelter partners this month</p>
            </div>
          </div>
        </section>

        <div className="grid gap-8 md:grid-cols-3">
          {products.map((product) => (
            <Card hover key={product.name}>
              <div className="h-64 overflow-hidden">
                <ImageWithFallback alt={product.name} className="h-full w-full object-cover" src={product.image} />
              </div>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <h2 className="text-xl font-black text-slate-900">{product.name}</h2>
                  <span className="text-lg font-black text-orange-500">{product.price}</span>
                </div>
                <div className="mt-3 flex text-amber-400">
                  {[1, 2, 3, 4, 5].map((item) => <Star className="h-4 w-4 fill-current" key={item} />)}
                </div>
              </CardHeader>
              <CardContent>
                <Button className="w-full" icon={<ShoppingBag className="h-5 w-5" />} onClick={() => handleAddToCart(product)}>Add to Cart</Button>
              </CardContent>
            </Card>
          ))}
        </div>
        {status ? (
          <p className="mt-8 rounded-2xl bg-orange-50 px-4 py-3 text-sm font-bold text-orange-600">
            {status}
          </p>
        ) : null}

        <div className="mt-10 rounded-[30px] border border-orange-100 bg-orange-50 p-6">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
              <div className="grid h-14 w-14 place-items-center rounded-2xl bg-white text-orange-500">
                <Package className="h-7 w-7" />
              </div>
              <div>
                <div className="text-xl font-black text-slate-900">Backend-ready checkout</div>
                <p className="text-sm text-slate-500">Cart, inventory, and payment handlers can connect here.</p>
              </div>
            </div>
            <Button variant="outline">View Cart</Button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ShopPage
