import { Calendar, Heart, MapPin, Search, ShieldCheck, SlidersHorizontal, Star } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { ImageWithFallback } from '../components/ImageWithFallback'
import { MotionReveal } from '../components/MotionReveal'

const pets = [
  {
    name: 'Luna',
    breed: 'Local Mix',
    age: '2 years',
    area: 'Dhanmondi',
    image: 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=900&auto=format&fit=crop&q=80',
    tags: ['Vaccinated', 'Calm', 'Family ready'],
  },
  {
    name: 'Milo',
    breed: 'Rescue Cat',
    age: '8 months',
    area: 'Banani',
    image: 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=900&auto=format&fit=crop&q=80',
    tags: ['Playful', 'Neutered', 'Indoor'],
  },
  {
    name: 'Bruno',
    breed: 'Street Puppy',
    age: '5 months',
    area: 'Uttara',
    image: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=900&auto=format&fit=crop&q=80',
    tags: ['Energetic', 'Dewormed', 'Training'],
  },
  {
    name: 'Maya',
    breed: 'Indie Dog',
    age: '3 years',
    area: 'Gulshan',
    image: 'https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=900&auto=format&fit=crop&q=80',
    tags: ['Gentle', 'Spayed', 'Leash trained'],
  },
]

function AdoptionPage({ onNavigate }) {
  return (
    <div className="min-h-screen bg-white">
      <section className="relative overflow-hidden px-4 py-12 sm:px-6 lg:px-8">
        <div className="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.18),_transparent_32%),linear-gradient(180deg,_#fff8ed_0%,_#ffffff_80%)]" />
        <div className="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[0.9fr_1.1fr]">
          <MotionReveal y={24}>
            <div className="mb-4 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              Adopt with confidence
            </div>
            <h1 className="max-w-2xl text-5xl font-black leading-none tracking-tight text-slate-900">
              Meet rescued pets waiting for a forever home.
            </h1>
            <p className="mt-5 max-w-2xl text-lg leading-8 text-slate-500">
              Browse verified rescue animals, compare care notes, and send adoption interest when
              you find a companion who fits your home.
            </p>
            <div className="mt-7 flex flex-wrap gap-4">
              <Button icon={<Heart className="h-5 w-5" />} onClick={() => onNavigate?.('register')} size="lg">
                Start Adoption
              </Button>
              <Button onClick={() => onNavigate?.('report')} size="lg" variant="outline">
                Report an Animal
              </Button>
            </div>
          </MotionReveal>
          <MotionReveal className="grid gap-4 sm:grid-cols-2" delay={0.08} y={24}>
            {['Verified shelters', 'Medical history', 'Adoption checks', 'Home support'].map((item) => (
              <div className="rounded-[28px] border border-orange-100 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.06)]" key={item}>
                <ShieldCheck className="mb-5 h-8 w-8 text-orange-500" />
                <div className="text-lg font-black text-slate-900">{item}</div>
                <p className="mt-2 text-sm leading-6 text-slate-500">
                  Clear rescue records and responsible handover steps for every match.
                </p>
              </div>
            ))}
          </MotionReveal>
        </div>
      </section>

      <section className="px-4 py-12 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-7xl">
          <div className="mb-8 flex flex-col gap-4 rounded-[30px] border border-slate-200 bg-white p-4 shadow-[0_16px_35px_rgba(15,23,42,0.05)] lg:flex-row">
            <label className="relative flex-1">
              <Search className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
              <input className="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 font-semibold outline-none focus:border-orange-300 focus:bg-white" placeholder="Search by name, breed, or area" />
            </label>
            <Button icon={<SlidersHorizontal className="h-5 w-5" />} variant="outline">
              Filters
            </Button>
          </div>

          <div className="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
            {pets.map((pet) => (
              <MotionReveal key={pet.name} y={22}>
                <Card hover>
                  <div className="relative h-60 overflow-hidden">
                    <ImageWithFallback alt={pet.name} className="h-full w-full object-cover" src={pet.image} />
                    <div className="absolute right-4 top-4 rounded-full bg-white/90 p-2 text-orange-500 shadow-lg">
                      <Heart className="h-5 w-5" />
                    </div>
                  </div>
                  <CardHeader>
                    <div className="flex items-start justify-between gap-3">
                      <div>
                        <h3 className="text-2xl font-black text-slate-900">{pet.name}</h3>
                        <p className="mt-1 font-bold text-orange-500">{pet.breed}</p>
                      </div>
                      <div className="flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-sm font-black text-amber-600">
                        <Star className="h-4 w-4 fill-current" />
                        4.9
                      </div>
                    </div>
                    <div className="mt-4 flex flex-wrap gap-3 text-sm font-bold text-slate-500">
                      <span className="inline-flex items-center gap-1"><Calendar className="h-4 w-4" />{pet.age}</span>
                      <span className="inline-flex items-center gap-1"><MapPin className="h-4 w-4" />{pet.area}</span>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="mb-5 flex flex-wrap gap-2">
                      {pet.tags.map((tag) => (
                        <span className="rounded-full bg-orange-50 px-3 py-1 text-xs font-black uppercase tracking-[0.12em] text-orange-600" key={tag}>
                          {tag}
                        </span>
                      ))}
                    </div>
                    <Button className="w-full" onClick={() => onNavigate?.('register')}>Meet {pet.name}</Button>
                  </CardContent>
                </Card>
              </MotionReveal>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}

export default AdoptionPage
