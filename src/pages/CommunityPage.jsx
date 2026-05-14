import { Heart, MessageCircle, Plus, Share2, Users } from 'lucide-react'
import { useState } from 'react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { ImageWithFallback } from '../components/ImageWithFallback'
import { communityPosts as posts } from '../app/rescueStore'
import { api } from '../services/api'

function CommunityPage() {
  const [status, setStatus] = useState('')

  const handleCreatePost = async () => {
    const draft = {
      title: 'New rescue update',
      text: 'Community post composer will connect here.',
    }

    setStatus('Opening community post workflow...')
    try {
      await api.createCommunityPost(draft)
      setStatus('Community post created.')
    } catch (error) {
      setStatus(`${error.message}. Backend endpoint pending at ${api.baseUrl}.`)
    }
  }

  return (
    <div className="min-h-screen bg-white px-4 py-12 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl">
        <div className="mb-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <div className="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              Rescue community
            </div>
            <h1 className="max-w-3xl text-5xl font-black leading-none tracking-tight text-slate-900">
              Local updates from people saving animals together.
            </h1>
          </div>
          <Button icon={<Plus className="h-5 w-5" />} onClick={handleCreatePost} size="lg">Create Post</Button>
        </div>
        {status ? (
          <p className="mb-8 rounded-2xl bg-orange-50 px-4 py-3 text-sm font-bold text-orange-600">
            {status}
          </p>
        ) : null}

        <div className="grid gap-8 lg:grid-cols-[1fr_360px]">
          <div className="grid gap-6">
            {posts.map((post) => (
              <Card hover key={post.title}>
                <div className="grid md:grid-cols-[260px_1fr]">
                  <ImageWithFallback alt={post.title} className="h-64 w-full object-cover md:h-full" src={post.image} />
                  <CardHeader>
                    <div className="mb-4 flex items-center gap-3">
                      <div className="grid h-11 w-11 place-items-center rounded-full bg-orange-100 font-black text-orange-600">
                        {post.author.split(' ').map((part) => part[0]).join('')}
                      </div>
                      <div>
                        <div className="font-black text-slate-900">{post.author}</div>
                        <div className="text-sm font-semibold text-slate-500">Community update</div>
                      </div>
                    </div>
                    <h2 className="text-2xl font-black text-slate-900">{post.title}</h2>
                    <p className="mt-3 leading-7 text-slate-500">{post.text}</p>
                    <div className="mt-6 flex gap-3">
                      <Button icon={<Heart className="h-4 w-4" />} size="sm" variant="outline">Support</Button>
                      <Button icon={<MessageCircle className="h-4 w-4" />} size="sm" variant="ghost">Comment</Button>
                      <Button icon={<Share2 className="h-4 w-4" />} size="sm" variant="ghost">Share</Button>
                    </div>
                  </CardHeader>
                </div>
              </Card>
            ))}
          </div>

          <aside className="grid gap-6 self-start">
            <Card className="bg-orange-50/70">
              <CardHeader>
                <Users className="mb-4 h-8 w-8 text-orange-500" />
                <h3 className="text-2xl font-black text-slate-900">Active rescue circles</h3>
                <p className="mt-2 text-sm leading-6 text-slate-500">Join area-based groups for reports, transport, foster care, and adoption support.</p>
              </CardHeader>
              <CardContent>
                {['Dhaka North', 'Chittagong City', 'Sylhet Shelter Friends'].map((group) => (
                  <div className="mb-3 rounded-2xl bg-white px-4 py-3 font-bold text-slate-700" key={group}>{group}</div>
                ))}
              </CardContent>
            </Card>
          </aside>
        </div>
      </div>
    </div>
  )
}

export default CommunityPage
