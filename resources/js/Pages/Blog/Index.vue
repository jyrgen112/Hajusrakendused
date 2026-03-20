<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto p-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">📝 Blog</h1>

      <!-- New Post Form (auth only) -->
      <div v-if="$page.props.auth.user" class="bg-white rounded-2xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">
          <span v-if="editingPost">Edit Post</span>
          <span v-else>New Post</span>
        </h2>
        <div class="space-y-3">
          <input v-model="postForm.title" type="text" placeholder="Title" class="w-full border rounded-xl px-4 py-2" />
          <textarea v-model="postForm.description" placeholder="Write your post..." class="w-full border rounded-xl px-4 py-2" rows="4"></textarea>
          <div class="flex gap-2">
            <button @click="submitPost" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700">
              <span v-if="editingPost">Update</span>
              <span v-else>Publish</span>
            </button>
            <button v-if="editingPost" @click="cancelEditPost" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-semibold hover:bg-gray-300">
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- Posts -->
      <div class="space-y-6">
        <div v-for="post in posts" :key="post.id" class="bg-white rounded-2xl shadow p-6">

          <!-- Post Header -->
          <div class="flex justify-between items-start mb-3">
            <div>
              <h2 class="text-xl font-bold text-gray-800">{{ post.title }}</h2>
              <p class="text-sm text-gray-400">by {{ post.user.name }} · {{ formatDate(post.created_at) }}</p>
            </div>
            <div v-if="$page.props.auth.user && $page.props.auth.user.id === post.user_id" class="flex gap-2">
              <button @click="startEditPost(post)" class="text-indigo-600 text-sm hover:underline">Edit</button>
              <button @click="deletePost(post.id)" class="text-red-500 text-sm hover:underline">Delete</button>
            </div>
          </div>

          <!-- Post Body -->
          <p class="text-gray-600 mb-6 whitespace-pre-line">{{ post.description }}</p>

          <!-- Comments -->
          <div class="border-t pt-4">
            <h3 class="text-sm font-semibold text-gray-500 mb-3">Comments ({{ post.comments.length }})</h3>
            <div class="space-y-3 mb-4">
              <div v-for="comment in post.comments" :key="comment.id" class="flex justify-between items-start bg-gray-50 rounded-xl p-3">
                <div>
                  <p class="text-sm font-semibold text-gray-700">{{ comment.user.name }}</p>
                  <p class="text-sm text-gray-600">{{ comment.body }}</p>
                </div>
                <button
                  v-if="$page.props.auth.user && ($page.props.auth.user.id === comment.user_id || $page.props.auth.user.is_admin)"
                  @click="deleteComment(comment.id)"
                  class="text-red-400 text-xs hover:underline ml-4"
                >Delete</button>
              </div>
            </div>

            <!-- Add Comment -->
            <div v-if="$page.props.auth.user" class="flex gap-2">
              <input
                v-model="commentForms[post.id]"
                type="text"
                placeholder="Write a comment..."
                class="flex-1 border rounded-xl px-3 py-2 text-sm"
                @keyup.enter="submitComment(post.id)"
              />
              <button @click="submitComment(post.id)" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700">
                Post
              </button>
            </div>
            <p v-else class="text-sm text-gray-400">
              <a href="/login" class="text-indigo-600 hover:underline">Log in</a> to comment.
            </p>
          </div>

        </div>

        <p v-if="posts.length === 0" class="text-center text-gray-400 py-12">No posts yet. Be the first to write one!</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  posts: Array,
})

const postForm = ref({ title: '', description: '' })
const editingPost = ref(null)
const commentForms = ref({})

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

function submitPost() {
  if (editingPost.value) {
    router.put('/posts/' + editingPost.value.id, postForm.value, {
      onSuccess: () => cancelEditPost()
    })
  } else {
    router.post('/posts', postForm.value, {
      onSuccess: () => { postForm.value = { title: '', description: '' } }
    })
  }
}

function startEditPost(post) {
  editingPost.value = post
  postForm.value = { title: post.title, description: post.description }
}

function cancelEditPost() {
  editingPost.value = null
  postForm.value = { title: '', description: '' }
}

function deletePost(id) {
  if (confirm('Delete this post?')) {
    router.delete('/posts/' + id)
  }
}

function submitComment(postId) {
  const body = commentForms.value[postId]
  if (!body) return
  router.post('/comments', { post_id: postId, body }, {
    onSuccess: () => { commentForms.value[postId] = '' }
  })
}

function deleteComment(id) {
  if (confirm('Delete this comment?')) {
    router.delete('/comments/' + id)
  }
}
</script>