import { motion } from 'framer-motion'
import {
  AlertCircle,
  CheckCircle,
  Heart,
  HomeIcon,
  MapPin,
  Sparkles,
  TrendingUp,
  Users,
} from 'lucide-react'
import { Button } from '../components/Button'
import { StatCard } from '../components/StatCard'
import { Card, CardContent, CardHeader } from '../components/Card'
import { ImageWithFallback } from '../components/ImageWithFallback'
import Footer from '../components/Footer'
import { MotionReveal } from '../components/MotionReveal'

function LandingPage({ onNavigate }) {
  const stats = [
    { icon: Heart, value: '12,543', label: 'Animals Rescued', color: 'text-orange-500' },
    { icon: Users, value: '2,847', label: 'Active Volunteers', color: 'text-amber-500' },
    { icon: HomeIcon, value: '156', label: 'Partner Shelters', color: 'text-emerald-500' },
    { icon: Sparkles, value: '8,921', label: 'Happy Adoptions', color: 'text-orange-500' },
  ]

  const howItWorks = [
    {
      step: '1',
      title: 'Report a Stray',
      description:
        'Spot a stray animal? Report it with location and photos through our easy-to-use platform.',
      icon: AlertCircle,
    },
    {
      step: '2',
      title: 'Volunteer Response',
      description:
        'Our network of volunteers receives instant notifications and responds to rescue requests nearby.',
      icon: Users,
    },
    {
      step: '3',
      title: 'Shelter Care',
      description:
        'Animals receive medical care, food, and love at our partner shelters until they find homes.',
      icon: Heart,
    },
    {
      step: '4',
      title: 'Find Forever Homes',
      description:
        'Match with your perfect companion through our smart adoption matching system.',
      icon: HomeIcon,
    },
  ]

  const successStories = [
    {
      name: 'Bella',
      type: 'Golden Retriever',
      story:
        'Found injured on the streets of Dhaka, now thriving with her loving family in Gulshan.',
      image: 'https://images.unsplash.com/photo-1633722715463-d30f4f325e24?w=800&auto=format&fit=crop&q=80',
    },
    {
      name: 'Whiskers',
      type: 'Rescue Cat',
      story:
        'Abandoned kitten rescued by volunteers, now bringing joy to a family in Chittagong.',
      image: 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=800&auto=format&fit=crop&q=80',
    },
    {
      name: 'Max',
      type: 'Street Puppy',
      story:
        'Rescued from harsh conditions, recovered at our shelter, and adopted by a veterinarian.',
      image: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=800&auto=format&fit=crop&q=80',
    },
  ]

  const testimonials = [
    {
      name: 'Sarah Rahman',
      role: 'Volunteer',
      text: 'Being part of StrayRescue has been the most rewarding experience. Every rescue makes a difference!',
      avatar: 'SR',
    },
    {
      name: 'Ahmed Khan',
      role: 'Adopter',
      text: 'I found my best friend through this platform. The matching process was smooth and the support was amazing.',
      avatar: 'AK',
    },
    {
      name: 'Dr. Fatima Noor',
      role: 'Shelter Partner',
      text: 'This platform has revolutionized animal rescue in Bangladesh. Truly making an impact.',
      avatar: 'FN',
    },
  ]

  const trustMetrics = ['120+ active rescue teams', '56 districts reached', 'Daily rescue updates']

  return (
    <div>
      <section className="relative overflow-hidden px-4 pb-12 pt-6 sm:px-6 lg:px-8">
        <div className="absolute inset-x-0 top-0 -z-10 h-[44rem] bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.24),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(249,115,22,0.15),_transparent_28%),linear-gradient(180deg,_#fffaf3_0%,_#fffdfb_58%,_#fff8f1_100%)]" />
        <div className="absolute left-10 top-12 -z-10 h-40 w-40 rounded-full bg-orange-200/20 blur-3xl" />
        <div className="absolute right-0 top-28 -z-10 h-64 w-64 rounded-full bg-amber-200/20 blur-3xl" />

        <div className="mx-auto grid max-w-7xl items-center gap-14 rounded-[40px] border border-orange-100/80 bg-white/70 p-6 shadow-[0_25px_60px_rgba(249,115,22,0.08)] backdrop-blur-sm md:grid-cols-2 md:p-10 xl:p-14">
          <motion.div
            initial={{ opacity: 0, y: 28 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
          >
            <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-orange-100 bg-white px-4 py-2 shadow-sm">
              <Heart className="h-4 w-4 fill-current text-orange-500" />
              <span className="text-sm font-bold text-slate-700">Saving Lives Together</span>
            </div>

            <h1 className="max-w-xl text-5xl font-black leading-[0.95] tracking-[-0.06em] text-slate-900 sm:text-6xl">
              Every Stray Deserves a <span className="text-orange-500">Second Chance</span>
            </h1>

            <p className="mt-6 max-w-xl text-lg leading-8 text-slate-500 sm:text-xl">
              Join Bangladesh&apos;s largest animal rescue network. Report strays, volunteer for
              rescues, or find your perfect companion.
            </p>

            <div className="mt-8 flex flex-wrap gap-4">
              <Button
                icon={<AlertCircle className="h-5 w-5" />}
                onClick={() => onNavigate?.('report')}
                size="lg"
              >
                Report Stray Animal
              </Button>

              <Button
                onClick={() => onNavigate?.('volunteer')}
                size="lg"
                variant="outline"
              >
                Become a Volunteer
              </Button>
            </div>

            <div className="mt-8 flex flex-wrap items-center gap-6 text-sm font-bold text-slate-500">
              <div className="flex items-center gap-2">
                <CheckCircle className="h-5 w-5 text-emerald-500" />
                <span>Free to Use</span>
              </div>
              <div className="flex items-center gap-2">
                <CheckCircle className="h-5 w-5 text-emerald-500" />
                <span>24/7 Support</span>
              </div>
              <div className="flex items-center gap-2">
                <CheckCircle className="h-5 w-5 text-emerald-500" />
                <span>Verified Shelters</span>
              </div>
            </div>

            <div className="mt-8 flex flex-wrap gap-3">
              {trustMetrics.map((item) => (
                <motion.div
                  className="rounded-full border border-slate-200/80 bg-white/90 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-500 shadow-sm"
                  initial={{ opacity: 0, y: 14 }}
                  key={item}
                  transition={{ duration: 0.4, delay: 0.15 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true, amount: 0.6 }}
                >
                  {item}
                </motion.div>
              ))}
            </div>
          </motion.div>

          <motion.div
            className="relative"
            initial={{ opacity: 0, y: 34, scale: 0.98 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            transition={{ duration: 0.7, delay: 0.08, ease: [0.22, 1, 0.36, 1] }}
          >
            <div className="relative overflow-hidden rounded-[32px] border border-white/40 shadow-[0_32px_70px_rgba(15,23,42,0.22)]">
              <ImageWithFallback
                alt="Rescued animals"
                className="h-[420px] w-full object-cover sm:h-[500px]"
                src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=1200&auto=format&fit=crop&q=80"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent" />
              <div className="absolute bottom-6 left-6 right-6 text-white">
                <p className="text-lg font-bold">Luna - Rescued & Adopted</p>
                <p className="mt-1 text-sm text-white/85">From street to sweet home in 2 weeks</p>
              </div>
            </div>

            <div className="absolute -bottom-6 right-4 hidden rounded-3xl border border-white/70 bg-white/95 p-4 shadow-[0_20px_45px_rgba(15,23,42,0.18)] md:block">
              <div className="flex items-center gap-3">
                <div className="rounded-2xl bg-emerald-50 p-3 text-emerald-500">
                  <TrendingUp className="h-6 w-6" />
                </div>
                <div>
                  <div className="text-2xl font-black tracking-tight text-slate-900">89%</div>
                  <div className="text-sm font-semibold text-slate-500">Success Rate</div>
                </div>
              </div>
            </div>

            <div className="absolute -left-6 top-8 hidden rounded-3xl border border-orange-100 bg-white/92 p-4 shadow-[0_18px_40px_rgba(15,23,42,0.12)] lg:block">
              <div className="text-xs font-black uppercase tracking-[0.22em] text-slate-400">
                Live impact
              </div>
              <div className="mt-2 text-2xl font-black tracking-tight text-slate-900">34</div>
              <div className="text-sm font-semibold text-slate-500">Rescues coordinated today</div>
            </div>
          </motion.div>
        </div>
      </section>

      <MotionReveal className="px-4 py-10 sm:px-6 lg:px-8" y={24}>
        <div className="mx-auto grid max-w-7xl grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <MotionReveal delay={0.04} key={stat.label} y={20}>
              <StatCard iconColor={stat.color} {...stat} />
            </MotionReveal>
          ))}
        </div>
      </MotionReveal>

      <MotionReveal className="px-4 py-16 sm:px-6 lg:px-8" id="community" y={28}>
        <div className="mx-auto max-w-7xl">
          <div className="mx-auto mb-16 max-w-2xl text-center">
            <div className="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              Rescue Network
            </div>
            <h2 className="text-4xl font-black tracking-tight text-slate-900">How It Works</h2>
            <p className="mt-4 text-lg text-slate-500">
              Four simple steps to make a life-changing difference
            </p>
          </div>

          <div className="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
            {howItWorks.map((item) => (
              <MotionReveal delay={0.05} key={item.step} y={24}>
                <Card hover className="bg-white/88 backdrop-blur-sm">
                  <CardHeader className="text-center">
                    <div className="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-orange-100 text-orange-500">
                      <item.icon className="h-8 w-8" />
                    </div>
                    <div className="mx-auto mb-3 grid h-8 w-8 place-items-center rounded-full bg-orange-500 text-sm font-black text-white">
                      {item.step}
                    </div>
                    <h3 className="text-lg font-black text-slate-900">{item.title}</h3>
                  </CardHeader>
                  <CardContent>
                    <p className="text-center text-sm leading-7 text-slate-500">{item.description}</p>
                  </CardContent>
                </Card>
              </MotionReveal>
            ))}
          </div>
        </div>
      </MotionReveal>

      <MotionReveal className="bg-white px-4 py-16 sm:px-6 lg:px-8" id="adopt" y={28}>
        <div className="mx-auto max-w-7xl">
          <div className="mb-16 text-center">
            <div className="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              Happy Endings
            </div>
            <h2 className="text-4xl font-black tracking-tight text-slate-900">Success Stories</h2>
            <p className="mt-4 text-lg text-slate-500">Lives transformed through compassion</p>
          </div>

          <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
            {successStories.map((story) => (
              <MotionReveal delay={0.05} key={story.name} y={24}>
                <Card hover>
                  <div className="relative h-64 overflow-hidden">
                    <ImageWithFallback
                      alt={story.name}
                      className="h-full w-full object-cover transition-transform duration-500 hover:scale-105"
                      src={story.image}
                    />
                  </div>
                  <CardHeader>
                    <h3 className="text-xl font-black text-slate-900">{story.name}</h3>
                    <p className="mb-3 mt-1 text-sm font-bold text-orange-500">{story.type}</p>
                    <p className="text-sm leading-7 text-slate-500">{story.story}</p>
                  </CardHeader>
                </Card>
              </MotionReveal>
            ))}
          </div>
        </div>
      </MotionReveal>

      <MotionReveal className="px-4 py-16 sm:px-6 lg:px-8" y={26}>
        <div className="mx-auto max-w-7xl rounded-[36px] bg-[linear-gradient(180deg,_rgba(255,247,237,0.7),_rgba(255,255,255,0.95))] p-8 shadow-[0_18px_45px_rgba(249,115,22,0.06)] md:p-10">
          <div className="mb-16 text-center">
            <div className="mb-3 inline-flex rounded-full bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600 shadow-sm">
              Community Voices
            </div>
            <h2 className="text-4xl font-black tracking-tight text-slate-900">What People Say</h2>
            <p className="mt-4 text-lg text-slate-500">Hear from our community</p>
          </div>

          <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
            {testimonials.map((testimonial) => (
              <MotionReveal delay={0.05} key={testimonial.name} y={22}>
                <Card className="bg-white/95">
                  <CardHeader>
                    <div className="mb-4 flex items-center gap-4">
                      <div className="flex h-12 w-12 items-center justify-center rounded-full bg-orange-500 text-sm font-black text-white">
                        {testimonial.avatar}
                      </div>
                      <div>
                        <div className="font-black text-slate-900">{testimonial.name}</div>
                        <div className="text-sm font-semibold text-slate-500">{testimonial.role}</div>
                      </div>
                    </div>
                    <p className="text-base italic leading-8 text-slate-500">
                      &quot;{testimonial.text}&quot;
                    </p>
                  </CardHeader>
                </Card>
              </MotionReveal>
            ))}
          </div>
        </div>
      </MotionReveal>

      <MotionReveal className="relative overflow-hidden px-4 py-16 sm:px-6 lg:px-8" id="report" y={24}>
        <div className="mx-auto max-w-7xl overflow-hidden rounded-[40px] bg-[linear-gradient(110deg,_#f97316_0%,_#fb923c_45%,_#fbbf24_100%)] px-6 py-14 text-center shadow-[0_28px_70px_rgba(249,115,22,0.28)] sm:px-10">
          <div className="absolute left-10 top-10 h-28 w-28 rounded-full bg-white/10 blur-2xl" />
          <div className="absolute bottom-0 right-8 h-36 w-36 rounded-full bg-black/10 blur-3xl" />
          <h2 className="text-4xl font-black tracking-tight text-white">Ready to Make a Difference?</h2>
          <p className="mt-4 text-xl leading-8 text-white/90">
            Join thousands of volunteers and animal lovers making Bangladesh a better place for
            strays
          </p>
          <div className="mt-8 flex flex-wrap justify-center gap-4">
            <Button
              className="bg-white/40 text-orange-600 hover:bg-orange-50"
              onClick={() => onNavigate?.('adopt')}
              size="lg"
            >
              Adopt a Pet
            </Button>
            <Button
              className="bg-slate-900 text-white hover:bg-slate-800"
              onClick={() => onNavigate?.('register')}
              size="lg"
            >
              Join Now
            </Button>
          </div>
        </div>
      </MotionReveal>

      <MotionReveal className="bg-white px-4 py-16 sm:px-6 lg:px-8" id="shelters" y={28}>
        <div className="mx-auto max-w-7xl rounded-[34px] bg-[linear-gradient(180deg,_#fff7ed_0%,_#fffbf6_100%)] p-8 shadow-[0_24px_50px_rgba(249,115,22,0.08)] md:p-12">
          <div className="grid items-center gap-8 md:grid-cols-2">
            <div>
              <div className="mb-3 inline-flex rounded-full bg-white px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600 shadow-sm">
                Shelter Access
              </div>
              <h3 className="text-3xl font-black tracking-tight text-slate-900">Nearby Shelters</h3>
              <p className="mt-4 max-w-lg text-lg leading-8 text-slate-500">
                Find partner shelters near you. Visit, volunteer, or support them directly.
              </p>
              <div className="mt-6">
                <Button
                  icon={<MapPin className="h-5 w-5" />}
                  onClick={() => onNavigate?.('shelters')}
                >
                  View All Shelters
                </Button>
              </div>
            </div>

            <div className="relative overflow-hidden rounded-[28px] border border-orange-100 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.07)]">
              <div className="h-64 rounded-[22px] bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.22),_transparent_30%),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_55%,_#ffedd5_100%)]" />
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="rounded-3xl border border-orange-100 bg-white/90 px-6 py-5 shadow-lg">
                  <div className="flex items-center gap-3">
                    <MapPin className="h-8 w-8 text-orange-500" />
                    <div>
                      <div className="font-black text-slate-900">Interactive Map</div>
                      <div className="text-sm font-semibold text-slate-500">
                        Shelter and rescue point preview
                      </div>
                    </div>
                  </div>
                  <div className="mt-4 grid grid-cols-2 gap-3 text-left text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                    <div className="rounded-2xl bg-orange-50 px-3 py-3">24 shelter zones</div>
                    <div className="rounded-2xl bg-orange-50 px-3 py-3">Verified intake teams</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </MotionReveal>

      <Footer />
    </div>
  )
}

export default LandingPage
